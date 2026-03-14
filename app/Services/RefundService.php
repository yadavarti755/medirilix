<?php

namespace App\Services;

use App\Repositories\RefundRepository;
use App\DTO\RefundDto;
use App\Services\CouponUsageService;
use App\Mail\RefundUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Enums\OrderStatus;
use App\Jobs\SendOrderEmailJob;
use App\Repositories\ReturnRequestRepository;

class RefundService
{
    protected $repository;
    protected CouponUsageService $couponUsageService;

    public function __construct()
    {
        $this->repository = new RefundRepository();
        $this->couponUsageService = new CouponUsageService();
    }

    public function initiateRefund($orderProductListId, $amount, $status = 'PENDING', $remarks = 'Refund initiated')
    {
        // Check if refund already exists
        $existing = $this->repository->findByOrderProductListId($orderProductListId);
        if ($existing) {
            return $existing;
        }

        $dto = new RefundDto(
            $orderProductListId,
            $amount,
            $status,
            $remarks,
            Auth::id() ?? null,
            Auth::id() ?? null
        );

        $refund = $this->repository->create((array) $dto);

        try {
            // Retrieve order to get email (via relation)
            // Ideally we might want to eager load but accessing via relation is fine here
            $order = $refund->orderProductList->order;
            SendOrderEmailJob::dispatch(
                $order->user->email ?? $order->billing_email,
                new RefundUpdated($refund, $status)
            );
        } catch (\Exception $e) {
            Log::error("Failed to send RefundUpdated email: " . $e->getMessage());
        }

        return $refund;
    }

    public function updateStatus($id, $status, $remarks, $updatedBy)
    {
        $result = $this->repository->update($id, [
            'refund_status' => $status,
            'remarks' => $remarks,
            'updated_by' => $updatedBy
        ]);

        if ($result) {
            try {
                $refund = $this->repository->findById($id);
                $order = $refund->orderProductList->order;

                // Sync with Return Request
                $returnRequestRepo = new ReturnRequestRepository();
                $returnRequest = $returnRequestRepo->findByOrderProductListId($refund->order_product_list_id);
                if ($returnRequest) {
                    $returnRequestRepo->update([
                        'return_status' => $status,
                        'updated_by' => $updatedBy
                    ], $returnRequest->id);
                }

                // Revert Coupon Usage if Refund is Completed
                // Assuming 'COMPLETED' or 'REFUND_COMPLETED' is the status string. 
                // Based on OrderStatus enum, it could be REFUND_COMPLETED. 
                // If the status string passed matches OrderStatus::REFUND_COMPLETED->value or similar. 
                // Let's check against typical "Success" statuses.
                if ($status == 'COMPLETED' || $status == 'REFUND_COMPLETED' || $status == OrderStatus::REFUND_COMPLETED->value) {
                    $this->couponUsageService->revertUsageForOrder($order->id);
                }

                SendOrderEmailJob::dispatch(
                    $order->user->email ?? $order->billing_email,
                    new RefundUpdated($refund, $status)
                );
            } catch (\Exception $e) {
                Log::error("Failed to sync status or send email: " . $e->getMessage());
            }
        }
        return $result;
    }

    public function getAllRefunds()
    {
        return $this->repository->getAll();
    }

    public function getRefundById($id)
    {
        return $this->repository->findById($id);
    }
}
