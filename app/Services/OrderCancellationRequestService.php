<?php

namespace App\Services;

use App\DTO\OrderCancellationRequestDto;
use App\DTO\OrderCancellationRequestMessageDto;
use App\Repositories\OrderCancellationRequestRepository;
use App\Repositories\OrderCancellationRequestMessageRepository;
use App\Jobs\SendOrderEmailJob;

class OrderCancellationRequestService
{
    private $requestRepository;
    private $messageRepository;
    private $refundService;

    public function __construct()
    {
        $this->requestRepository = new OrderCancellationRequestRepository();
        $this->messageRepository = new OrderCancellationRequestMessageRepository();
        $this->refundService = new RefundService();
    }

    public function processCancellationRequest(OrderCancellationRequestDto $dto)
    {
        $orderProduct = \App\Models\OrderProductList::find($dto->order_product_list_id);

        if (!$orderProduct) {
            throw new \Exception("Order Product not found.");
        }

        // Time Limit Check
        $timeLimit = config('app.cancellation_time_limit_hours', env('CANCELLATION_TIME_LIMIT_HOURS', 24));
        if ($orderProduct->created_at->diffInHours(now()) > $timeLimit) {
            throw new \Exception("Cancellation is only allowed within $timeLimit hours of order creation.");
        }

        if ($orderProduct->product_order_status != 'PLACED') {
            throw new \Exception("Cancellation requests can only be created when the order status is 'PLACED'.");
        }

        // Check cancellation method
        $method = config('app.cancellation_method', env('CANCELLATION_METHOD', 'REQUEST'));

        if ($method === 'DIRECT') {
            return $this->processDirectCancellation($orderProduct, $dto);
        } else {
            return $this->createRequest($dto);
        }
    }

    private function processDirectCancellation($orderProduct, $dto)
    {
        // specific reasoning
        $reasonTitle = 'Cancellation';
        if ($dto->cancel_reason_id) {
            $reason = \App\Models\CancelReason::find($dto->cancel_reason_id);
            if ($reason) {
                $reasonTitle = $reason->title;
            }
        }

        // Update OrderProductList
        $orderProduct->product_order_status = 'CANCELLED';
        $orderProduct->cancel_reason = $dto->description; // Using description as user comment/reason
        $orderProduct->remarks = $reasonTitle; // Using selected reason title
        $orderProduct->status_changed_date = now();
        $orderProduct->status_changed_by = $dto->user_id;
        $orderProduct->save();

        // Initiate Refund
        $this->refundService->initiateRefund($orderProduct->id, $orderProduct->total_price);

        return (object) ['message' => 'Order cancelled and refund initiated successfully.'];
    }

    public function createRequest(OrderCancellationRequestDto $dto)
    {
        // Check if there is already an OPEN request? 
        // For now, allowing multiple as requested, but generally we might want to check for 'Pending' ones.
        // User asked to "create many cancellation request for the single product." 
        // I will assume business logic allows parallel or sequential requests.

        $request = $this->requestRepository->create([
            'order_product_list_id' => $dto->order_product_list_id,
            'user_id' => $dto->user_id,
            'cancel_reason_id' => $dto->cancel_reason_id,
            'description' => $dto->description,
            'status' => $dto->status,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        try {
            $adminEmail = config('app.admin_email');
            if ($adminEmail) {
                if ($adminEmail) {
                    SendOrderEmailJob::dispatch($adminEmail, new \App\Mail\OrderCancellationAdminNotification($request));
                }
            }

            // Notify User
            $user = \App\Models\User::find($dto->user_id);
            if ($user) {
                if ($user) {
                    SendOrderEmailJob::dispatch($user->email, new \App\Mail\OrderCancellationReceived($request));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send OrderCancellation Notification: " . $e->getMessage());
        }

        return $request;
    }

    public function createMessage(OrderCancellationRequestMessageDto $dto)
    {
        $message = $this->messageRepository->create([
            'order_cancellation_request_id' => $dto->order_cancellation_request_id,
            'message_by' => $dto->message_by,
            'message' => $dto->message,
        ]);

        // If message is from Admin (or just not the request creator?), notify the User
        // Ideally we check if message_by != request->user_id
        try {
            $request = $this->requestRepository->findById($dto->order_cancellation_request_id);
            if ($request && $dto->message_by != $request->user_id) {
                $user = \App\Models\User::find($request->user_id);
                if ($user) {
                    if ($user) {
                        SendOrderEmailJob::dispatch($user->email, new \App\Mail\OrderCancellationMessage($message));
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send OrderCancellationMessage email: " . $e->getMessage());
        }

        return $message;
    }

    public function updateStatus($id, $status, $userId)
    {
        $updated = $this->requestRepository->update([
            'status' => $status,
            'status_changed_by' => $userId,
            'updated_by' => $userId,
        ], $id);

        if ($updated && $status == 'Closed') {
            try {
                $request = $this->requestRepository->findById($id);
                if ($request) {
                    $user = \App\Models\User::find($request->user_id);
                    if ($user) {
                        if ($user) {
                            SendOrderEmailJob::dispatch($user->email, new \App\Mail\OrderCancellationClosed($request));
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send OrderCancellationClosed email: " . $e->getMessage());
            }
        }

        return $updated;
    }

    public function getRequestByProductListId($id)
    {
        return $this->requestRepository->findByOrderProductListId($id);
    }

    public function findAll()
    {
        return $this->requestRepository->findAll();
    }
}
