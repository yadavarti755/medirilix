<?php

namespace App\Services;

use App\DTO\ReturnRequestDto;
use App\Enums\OrderStatus;
use App\Models\OrderProductList;
use App\Repositories\ReturnRequestRepository;
use Illuminate\Support\Facades\Config;

class ReturnRequestService
{
    protected $repository;

    protected $refundService;

    public function __construct()
    {
        $this->repository = new ReturnRequestRepository();
        $this->refundService = new RefundService();
    }

    public function create(ReturnRequestDto $dto)
    {
        // 1. Create Return Request
        $returnRequest = $this->repository->create([
            'user_id' => $dto->user_id,
            'order_product_list_id' => $dto->order_product_list_id,
            'return_list_id' => $dto->return_list_id,
            'return_description' => $dto->return_description,
            'return_status' => OrderStatus::RETURN_REQUESTED->value,
            'created_by' => $dto->created_by,
        ]);

        // 2. Update OrderProductList Status
        $orderProduct = OrderProductList::find($dto->order_product_list_id);
        if ($orderProduct) {
            $orderProduct->order_status = OrderStatus::RETURN_REQUESTED->value;
            $orderProduct->save();
        }

        return $returnRequest;
    }

    public function updateStatus($id, $status, $pickupDetails = null, $updatedBy = null, $refundData = [])
    {
        // 1. Update Return Request
        $data = [
            'return_status' => $status,
            'updated_by' => $updatedBy,
        ];

        if ($status == OrderStatus::RETURN_APPROVED->value && $pickupDetails) {
            $data['return_pickup_details'] = $pickupDetails;
        }

        $returnRequest = $this->repository->update($data, $id);

        // 2. Update OrderProductList Status
        if ($returnRequest) {
            $orderProduct = OrderProductList::find($returnRequest->order_product_list_id);
            if ($orderProduct) {
                $orderProduct->product_order_status = $status;
                $orderProduct->save();

                // 3. Initiate Refund if status is REFUND_INITIATED
                if (isset(Config::get('constants.order_status_codes')['REFUND_INITIATED']) && $status == Config::get('constants.order_status_codes')['REFUND_INITIATED']) {
                    $itemTotal = $orderProduct->total_price;
                    $discountAmount = $orderProduct->discount_amount ?? 0;
                    $refundableAmount = max(0, $itemTotal - $discountAmount);

                    $amount = isset($refundData['amount']) && $refundData['amount'] ? $refundData['amount'] : $refundableAmount;
                    $remarks = isset($refundData['remarks']) ? $refundData['remarks'] : 'Refund initiated from return request';

                    $this->refundService->initiateRefund(
                        $returnRequest->order_product_list_id,
                        $amount,
                        $status,
                        $remarks
                    );
                }
            }
        }

        return $returnRequest;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->find($id);
    }
}
