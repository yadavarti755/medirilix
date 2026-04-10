<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\DTO\OrderProductShippingDetailDto;
use App\DTO\OrderProductListDto;
use App\Http\Requests\StoreOrderProductShippingDetailRequest;
use App\Services\OrderProductShippingDetailService;
use App\Services\OrderProductListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Jobs\SendOrderEmailJob;

class OrderProductShippingDetailController extends Controller
{
    protected $service;
    protected $orderProductListService;

    public function __construct()
    {
        $this->service = new OrderProductShippingDetailService();
        $this->orderProductListService = new OrderProductListService();
    }

    public function store(StoreOrderProductShippingDetailRequest $request)
    {
        try {
            $orderProductListId = Crypt::decryptString($request->product_list_id);
            $userId = \Illuminate\Support\Facades\Auth::id();

            // Check if shipping detail exists
            $existingDetail = $this->service->findByOrderProductListId($orderProductListId);

            $dto = new OrderProductShippingDetailDto(
                $orderProductListId,
                $request->order_status,
                $request->file('shipment_photos') ? $request->file('shipment_photos') : null,
                $request->shipment_details ?? ($existingDetail ? $existingDetail->shipping_details : null),
                $request->dhl_tracking_id ?? ($existingDetail ? $existingDetail->dhl_tracking_id : null),
                $userId,
                $userId
            );

            if ($existingDetail) {
                $result = $this->service->update($dto, $existingDetail->id);
            } else {
                $result = $this->service->create($dto);
            }

            if (!$result) {
                return Response::json([
                    'success' => false,
                    'message' => 'Failed to save shipping details',
                ], 500);
            }

            // Update Order Product List Status
            $listDto = new OrderProductListDto(
                user_id: null,
                order_number: null,
                product_id: null,
                product_order_status: $request->order_status,
                status_changed_date: now(),
                status_changed_by: $userId,
                remarks: $request->remarks ?? null,
                cancel_reason: $request->cancel_reason ?? null,
                updated_by: $userId
            );

            $this->orderProductListService->updateStatus($listDto, $orderProductListId);

            // Send Email Notifications
            try {
                $orderProductList = \App\Models\OrderProductList::find($orderProductListId);
                if ($orderProductList && $orderProductList->order) {
                    $order = $orderProductList->order;
                    $user = \App\Models\User::find($order->user_id);
                    $email = $user->email ?? $order->billing_email;

                    if ($email) {
                        if ($request->order_status == \App\Enums\OrderStatus::SHIPPED->value) {
                            SendOrderEmailJob::dispatch($email, new \App\Mail\OrderShipped($order->order_number, $orderProductList, $user));
                        } elseif ($request->order_status == \App\Enums\OrderStatus::DELIVERED->value) {
                            SendOrderEmailJob::dispatch($email, new \App\Mail\OrderDelivered($order->order_number, $orderProductList, $user));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to send shipping/delivery email: " . $e->getMessage());
            }

            return Response::json([
                'success' => true,
                'message' => 'Product status and shipping details updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Order Product Status Update Failed: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'message' => 'Server failed while processing request: ' . $e->getMessage()
            ], 500);
        }
    }
}
