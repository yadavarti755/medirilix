<?php

namespace App\Services;

use App\DTO\OrderAddressDto;
use App\DTO\OrderDto;
use App\DTO\OrderHistoryDto;
use App\DTO\OrderProductListDto;
use App\DTO\OrderStatusUpdateDto;
use App\Repositories\OrderRepository;
use App\Repositories\OrderProductListRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\ReturnRequestRepository;
use App\Repositories\ShipRocketOrderShippingDetailsRepository;
use App\Traits\OrderShippingTrait;
use App\Traits\AddressTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendOrderEmailJob;

use Illuminate\Support\Facades\Log;
use App\Mail\OrderPlaced;
use App\Mail\OrderShipped;
use App\Mail\OrderDelivered;
use App\Mail\AdminOrderCancelled;
use App\Mail\OrderReturn;
use App\Mail\ReturnRequestAdminNotification;
use App\Models\User;
use App\Models\Coupon;
use App\DTO\CouponUsageDto;
use App\Services\CouponUsageService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Services\ProductService;

class OrderService
{
    use AddressTrait, OrderShippingTrait;

    protected OrderRepository $orderRepository;
    protected OrderProductListService $orderProductListService;
    protected OrderHistoryService $orderHistoryService;
    protected OrderAddressService $orderAddressService;
    protected CouponUsageService $couponUsageService;
    protected ProductService $productService;

    // Repositories for existing methods (legacy/admin support)
    protected OrderProductListRepository $orderProductListRepository;
    protected OrderHistoryRepository $orderHistoryRepository;
    protected ReturnRequestRepository $returnRequestRepository;

    protected ShipRocketOrderShippingDetailsRepository $shipRocketRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();

        // Services for new Refactored logic
        $this->orderProductListService = new OrderProductListService();
        $this->orderHistoryService = new OrderHistoryService();
        $this->orderAddressService = new OrderAddressService();
        $this->orderAddressService = new OrderAddressService();
        $this->couponUsageService = new CouponUsageService();
        $this->productService = new ProductService();

        // Repositories for existing logic
        $this->orderProductListRepository = new OrderProductListRepository();
        $this->orderHistoryRepository = new OrderHistoryRepository();
        $this->returnRequestRepository = new ReturnRequestRepository();
        $this->shipRocketRepository = new ShipRocketOrderShippingDetailsRepository();
    }

    // ... (keep usage of this->createOrderWithDetails etc)

    public function createOrderWithDetails(OrderDto $orderDto, array $cartItems, $shippingAddress)
    {
        DB::beginTransaction();
        try {
            // 1. Create Order
            $order = $this->create($orderDto);

            // 2. Create Product List
            foreach ($cartItems as $item) {
                // Ensure item structure matches what controller sends (from session cart)
                // Controller uses: ['id'=>..., 'product_name'=>..., 'image'=>..., 'price'=>..., 'qty'=>...]
                // Cart structure from session might be:
                // [product_id, name, price, qty, image, attributes] -> indexed array
                // OR ['product_id'=>..., 'name'=>..., ...] -> associative array (based on recent cart helper changes)

                // Based on recent helpers.php changes, cart is associative array now with keys:
                // 'product_id', 'name', 'price', 'qty', 'image', 'attributes'

                // Calculate Tax per Item
                $itemDiscount = $item['discount_amount'] ?? 0;
                $itemTotal = $item['price'] * $item['qty'];
                $taxableAmount = max(0, $itemTotal - $itemDiscount);
                $taxRate = config('constants.tax_percentage', 0);
                $taxAmount = ($taxableAmount * $taxRate) / 100;

                $productListDto = new OrderProductListDto(
                    $orderDto->user_id,
                    $orderDto->order_number,
                    $item['product_id'],
                    basename($item['image']),
                    $item['name'],
                    $item['attributes']['size'] ?? null,
                    null, // material
                    $item['price'],
                    $item['qty'],
                    $item['price'] * $item['qty'],
                    OrderStatus::PENDING->value, // Pending Status
                    now(), // status_changed_date
                    $orderDto->created_by, // status_changed_by
                    null, // remarks
                    null, // cancel_reason
                    $itemDiscount, // Discount per item
                    $taxAmount, // Tax per item
                    $orderDto->created_by, // created_by
                    $orderDto->created_by // updated_by
                );
                $this->orderProductListService->create($productListDto);
            }

            // 3. Create Order Address
            $addressDto = new OrderAddressDto(
                $orderDto->user_id,
                $orderDto->order_number,
                $shippingAddress->name ?? $shippingAddress->person_name ?? 'N/A', // Check property names on Address model/object
                $shippingAddress->person_contact_number ?? $shippingAddress->person_contact_number ?? null,
                $shippingAddress->person_alt_contact_number ?? null,
                $shippingAddress->address ?? '',
                $shippingAddress->locality ?? '',
                $shippingAddress->landmark ?? '',
                $shippingAddress->city,
                $shippingAddress->state,
                $shippingAddress->country,
                $shippingAddress->pincode,
                $orderDto->created_by,
                $orderDto->created_by
            );
            $this->orderAddressService->create($addressDto);

            // 4. Create History
            $historyDto = new OrderHistoryDto(
                $orderDto->user_id,
                $orderDto->order_number,
                $orderDto->order_status,
                now(),
                'Order initiated.',
                $orderDto->created_by,
                $orderDto->created_by
            );
            $this->orderHistoryService->create($historyDto);

            // 5. Log Coupon Usage
            if ($orderDto->coupon_code && $orderDto->coupon_data) {
                // Determine coupon ID from data
                $couponData = is_string($orderDto->coupon_data) ? json_decode($orderDto->coupon_data, true) : $orderDto->coupon_data;
                // Assuming we stored coupon object or id in coupon_data or can fetch by code.
                // Better: OrderDto should probably carry coupon_id if we want relational integrity or look it up.
                // Let's look it up by code to be safe and simple.
                $coupon = Coupon::where('code', $orderDto->coupon_code)->first();
                if ($coupon) {
                    $usageDto = new CouponUsageDto(
                        $coupon->id,
                        $orderDto->user_id,
                        $order->id
                    );
                    $this->couponUsageService->logUsage($usageDto);
                }
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order Creation Failed: " . $e->getMessage());
            return null;
        }
    }

    public function updatePaymentStatus($orderId, $orderStatus, $paymentStatus, $transactionId = null)
    {
        $order = $this->findById($orderId);
        if ($order) {
            $order->order_status = $orderStatus;
            $order->payment_status = $paymentStatus;
            $order->save();

            // Add history
            $historyDto = new OrderHistoryDto(
                $order->user_id,
                $order->order_number,
                $orderStatus,
                now(),
                "Payment verified. Status: $paymentStatus. Transaction ID: $transactionId",
                Auth::id(),
                Auth::id()
            );
            $this->orderHistoryService->create($historyDto);

            // Send Order Placed Email
            if ($paymentStatus == 'SUCCESS' && in_array($orderStatus, [OrderStatus::PLACED->value, OrderStatus::PROCESSING->value])) {
                try {
                    $adminEmail = config('app.admin_email');
                    $mailable = new OrderPlaced($order);
                    SendOrderEmailJob::dispatch($order->user->email ?? $order->billing_email, $mailable)
                        ->onQueue('default');

                    if ($adminEmail) {
                        SendOrderEmailJob::dispatch($adminEmail, $mailable)->onQueue('default');
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send Order Placed Email: " . $e->getMessage());
                }
            } elseif (in_array($orderStatus, [OrderStatus::FAILED->value, OrderStatus::CANCELLED->value]) || $paymentStatus == PaymentStatus::FAILED->value) {
                // Revert Coupon Usage if Payment Failed or Order Cancelled (e.g. at gateway)
                $this->couponUsageService->revertUsageForOrder($order->id);
            }

            // Decrement Stock if Payment Success
            if ($paymentStatus == PaymentStatus::COMPLETED->value && in_array($orderStatus, [OrderStatus::PLACED->value, OrderStatus::PROCESSING->value])) {
                foreach ($order->orderProductList as $item) {
                    $this->productService->decrementStock($item->product_id, $item->quantity);
                }
            }
        }
    }

    public function findForUser($userId)
    {
        return $this->orderRepository->findForUser($userId);
    }

    public function findForAdmin($filters)
    {
        return $this->orderRepository->findForAdmin($filters);
    }

    public function findById($id)
    {
        return $this->orderRepository->findById($id);
    }

    public function findByOrderNumber($orderNumber)
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function findByOrderNumberAndUser($orderNumber, $userId)
    {
        return $this->orderRepository->findByOrderNumberAndUser($orderNumber, $userId);
    }

    /**
     * Change Order Status (Admin)
     */
    public function changeOrderStatus(OrderStatusUpdateDto $dto)
    {
        DB::beginTransaction();
        try {
            $orderNumber = $dto->order_number;
            $status = $dto->order_status;
            $updatedBy = $dto->updated_by;

            $orderDetails = $this->orderRepository->findByOrderNumber($orderNumber);
            if (!$orderDetails) {
                throw new \Exception('Order not found.');
            }

            $cancel_reason = '';
            // Status 5 = CANCELLED
            if ($status == 5) {
                $cancel_reason = $dto->remarks;
            }

            $result = $this->orderRepository->updateByOrderNumber($orderNumber, [
                'order_status' => $status,
                'order_status_changed_date' => now(),
                'cancel_reason' => $cancel_reason,
                'updated_by' => $updatedBy,
                'updated_at' => now()
            ]);

            if (!$result) {
                throw new \Exception('Failed to update order status.');
            }

            // Update product status
            // $this->orderProductListRepository->updateByOrderNumber($orderNumber, [
            //     'product_order_status' => $status,
            //     'status_changed_date' => now(),
            //     'updated_by' => $updatedBy,
            //     'updated_at' => now()
            // ]);

            // Insert data into order history
            $this->orderHistoryRepository->create([
                'user_id' => $orderDetails->user_id,
                'order_number' => $orderNumber,
                'order_status' => $status,
                'status_changed_date' => now(),
                'remarks' => $dto->remarks,
                'created_by' => $updatedBy,
                'created_at' => now(),
                'updated_by' => $updatedBy,
                'updated_at' => now()
            ]);

            if ($status == OrderStatus::CANCELLED->value) {
                // Revert Coupon Usage
                DB::table('coupon_usages')->where('order_id', $orderDetails->id)->delete();

                try {
                    $user = User::find($orderDetails->user_id);
                    SendOrderEmailJob::dispatch($user->email, new AdminOrderCancelled($orderNumber, $orderDetails));
                } catch (\Exception $e) {
                    Log::error("Failed to send status change email: " . $e->getMessage());
                }
            } elseif ($status == OrderStatus::SHIPPED->value) {
                // Email Notifications
                try {
                    $user = User::find($orderDetails->user_id);
                    SendOrderEmailJob::dispatch($user->email, new OrderShipped($orderNumber, $orderDetails, $user));
                } catch (\Exception $e) {
                    Log::error("Failed to send status change email: " . $e->getMessage());
                }
            } elseif ($status == OrderStatus::DELIVERED->value) {
                try {
                    $user = User::find($orderDetails->user_id);
                    SendOrderEmailJob::dispatch($user->email, new OrderDelivered($orderNumber, $orderDetails, $user));
                } catch (\Exception $e) {
                    Log::error("Failed to send status change email: " . $e->getMessage());
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel User Order
     */
    public function cancelUserOrder($orderId, $reason, $userId, $userEmail)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->findById($orderId);
            if (!$order || $order->user_id != $userId) {
                throw new \Exception('Invalid order.');
            }

            $allowedStatuses = [
                OrderStatus::PLACED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::SHIPPED->value
            ];

            if (!in_array($order->order_status, $allowedStatuses)) {
                throw new \Exception('Order cannot be cancelled at this stage.');
            }

            $currentStatus = OrderStatus::CANCELLED->value;

            $order->update([
                'order_status' => $currentStatus,
                'order_status_changed_date' => now(),
                'cancel_reason' => $reason
            ]);

            $this->orderProductListRepository->updateByOrderNumber($order->order_number, [
                'product_order_status' => $currentStatus,
                'status_changed_date' => now(),
                'updated_at' => now()
            ]);

            $this->orderHistoryRepository->create([
                'user_id' => $userId,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'status_changed_date' => now(),
                'remarks' => $reason,
                'created_by' => $userEmail,
                'updated_by' => $userEmail
            ]);

            // Mail::to($userEmail)->send(new OrderCancelled($order->order_number, $order));
            try {
                SendOrderEmailJob::dispatch($userEmail, new AdminOrderCancelled($order->order_number, $order));
            } catch (\Exception $e) {
                Log::error("Failed to send AdminOrderCancelled email: " . $e->getMessage());
            }

            // Revert Coupon Usage
            $this->couponUsageService->revertUsageForOrder($order->id);


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Return User Order
     */
    public function returnUserOrder($orderId, $products, $reason, $comment, $userId, $userEmail)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->findById($orderId);
            if (!$order || $order->user_id != $userId) {
                throw new \Exception('Invalid order.');
            }

            // Note: check_return_date_validity is a helper function. Assuming it is available globally.
            if (function_exists('check_return_date_validity') && check_return_date_validity($order->order_status_changed_date)) {
                throw new \Exception('Return date expired. You can not return order at this stage.');
            }

            if ($order->order_status != OrderStatus::DELIVERED->value) { // 4 = DELIVERED
                throw new \Exception('You can not return order at this time.');
            }

            $return_code = generateReturnCode(); // Assuming global helper

            $requestedStatus = OrderStatus::RETURN_REQUESTED->value;
            $cancelledStatus = OrderStatus::RETURN_CANCELLED->value;

            // Log activity to debug
            Log::info("Initiating Return for Order: {$order->order_number}, Products: " . json_encode($products));

            foreach ($products as $productId) {
                // Check existing return
                $excludedStatuses = [
                    OrderStatus::RETURN_REJECTED->value,
                    OrderStatus::RETURN_CANCELLED->value
                ];
                $existingActiveRequest = $this->returnRequestRepository->checkExists($userId, $order->order_number, $productId, $excludedStatuses);

                if ($existingActiveRequest) {
                    throw new \Exception('Return request already exists for one or more selected products.');
                }

                $existing = $this->returnRequestRepository->findByOrderNumber($userId, $order->order_number, $requestedStatus);
                if ($existing) {
                    $return_code = $existing->return_code;
                }

                $returnRequest = $this->returnRequestRepository->create([
                    'user_id' => $userId,
                    'order_number' => $order->order_number,
                    'return_code' => $return_code,
                    'product_id' => $productId,
                    'return_status' => $requestedStatus,
                    'return_reason' => $reason,
                    'return_date' => now(),
                    'comment' => $comment,
                    'created_by' => $userEmail,
                    'updated_by' => $userEmail
                ]);

                // Update product status - Explicitly using 'product_order_status'
                $this->orderProductListRepository->updateByProductAndOrderAndUser($productId, $order->order_number, $userId, [
                    'product_order_status' => $requestedStatus, // Ensure this matches DB column
                    'status_changed_date' => now(),
                ]);

                // Send Admin Notification for each product or once?
                // Sending once per request loop might be too much if multiple products, but distinct requests
                // The loop creates distinct return requests.
                try {
                    $adminEmail = config('app.admin_email');
                    if ($adminEmail) {
                        SendOrderEmailJob::dispatch($adminEmail, new ReturnRequestAdminNotification($returnRequest));
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send ReturnRequestAdminNotification: " . $e->getMessage());
                }
            }

            $this->orderHistoryRepository->create([
                'user_id' => $userId,
                'order_number' => $order->order_number,
                'order_status' => $requestedStatus,
                'status_changed_date' => now(),
                'remarks' => $comment,
                'created_by' => $userEmail,
                'updated_by' => $userEmail
            ]);

            try {
                SendOrderEmailJob::dispatch($userEmail, new OrderReturn($order->order_number, $order));
            } catch (\Exception $e) {
                Log::error("Failed to send OrderReturn email: " . $e->getMessage());
            }


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel Return
     */
    public function cancelReturnUserOrder($orderNumber, $productId, $userId, $userEmail)
    {
        DB::beginTransaction();
        try {
            $requestedStatus = OrderStatus::RETURN_REQUESTED->value;
            $cancelledStatus = OrderStatus::RETURN_CANCELLED->value;

            $this->returnRequestRepository->updateStatus(
                $orderNumber,
                $productId,
                $userId,
                $requestedStatus,
                $cancelledStatus,
                $userEmail
            );

            $this->orderProductListRepository->updateByProductAndOrderAndUser($productId, $orderNumber, $userId, [
                'product_order_status' => $cancelledStatus,
                'status_changed_date' => now(),
            ]);

            $this->orderHistoryRepository->create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_status' => $cancelledStatus,
                'status_changed_date' => now(),
                'remarks' => 'Return Cancelled by User',
                'created_by' => $userEmail,
                'updated_by' => $userEmail
            ]);

            DB::commit();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function create(OrderDto $orderDto)
    {
        return $this->orderRepository->create([
            'user_id' => $orderDto->user_id,
            'order_number' => $orderDto->order_number,
            'order_date' => $orderDto->order_date,
            'subtotal_price' => $orderDto->subtotal_price,
            'additional_charges' => $orderDto->shipping_price + $orderDto->tax_price,
            'total_price' => $orderDto->total_price,
            'payment_type' => $orderDto->payment_type,
            'order_status' => $orderDto->order_status,
            'payment_status' => $orderDto->payment_status,
            'created_by' => $orderDto->created_by,
            'updated_by' => $orderDto->updated_by,
            'discount_amount' => $orderDto->discount_amount,
            'coupon_code' => $orderDto->coupon_code,
            'coupon_data' => is_array($orderDto->coupon_data) ? json_encode($orderDto->coupon_data) : $orderDto->coupon_data,
            'tax_amount' => $orderDto->tax_amount,
            'shipping_charges' => $orderDto->shipping_charges,
        ]);
    }

    public function update(OrderDto $orderDto, int $id)
    {
        return $this->orderRepository->update((array) $orderDto, $id);
    }

    public function delete(int $id)
    {
        return $this->orderRepository->delete($id);
    }

    public function getAllReturnRequests($filters = [])
    {
        return $this->returnRequestRepository->findAllAdmin($filters);
    }

    public function getShipRocketDetails($orderNumber)
    {
        return $this->shipRocketRepository->findByOrderNumber($orderNumber);
    }

    public function approveReturnRequest($orderNumber, $returnCode, $returnIds, $approvalStatus, $remarks, $updatedBy, $additionalData = [])
    {
        DB::beginTransaction();
        try {
            $orderDetails = $this->orderRepository->findByOrderNumber($orderNumber);

            foreach ($returnIds as $rid) {
                $return_request = $this->returnRequestRepository->find($rid); // Use repository

                $this->returnRequestRepository->updateStatus(
                    $orderNumber,
                    $return_request->product_id,
                    $orderDetails->user_id,
                    $return_request->return_status, // current status not strictly checked here as per controller logic, or use id to update
                    $approvalStatus,
                    $updatedBy
                );

                // Use repository update instead of Model update
                $this->returnRequestRepository->update([
                    'return_status' => $approvalStatus,
                    'ag_remarks' => $remarks
                ], $rid);

                $this->orderProductListRepository->updateByProductAndOrderAndUser($return_request->product_id, $orderNumber, $orderDetails->user_id, [
                    'product_order_status' => $approvalStatus
                ]);
            }

            $this->orderHistoryRepository->create([
                'user_id' => $orderDetails->user_id,
                'order_number' => $orderNumber,
                'order_status' => $approvalStatus,
                'status_changed_date' => now(),
                'remarks' => $remarks,
                'created_by' => $updatedBy,
                'created_at' => now(),
                'updated_by' => $updatedBy,
                'updated_at' => now()
            ]);

            // ShipRocket
            if ($approvalStatus == 8 && isset($additionalData['box_data'])) {
                $return_requests = $this->returnRequestRepository->findByReturnCode($returnCode); // Use repository

                $spResult = $this->pushReturnRequestOrderToShipRocket($return_requests, $orderNumber, $additionalData['box_data']);

                if ($spResult['status'] != true) {
                    throw new \Exception($spResult['message'] ?? 'ShipRocket Error');
                }
            }

            DB::commit();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
