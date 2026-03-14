<?php

namespace App\Services;

use App\DTO\OrderProductListDto;
use App\Repositories\OrderProductListRepository;

class OrderProductListService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new OrderProductListRepository();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->repository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(OrderProductListDto $dto)
    {
        $result = $this->repository->create([
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'product_id' => $dto->product_id,
            'product_featured_image' => $dto->product_featured_image,
            'product_name' => $dto->product_name,
            'size' => $dto->size,
            'material' => $dto->material,
            'price' => $dto->price,
            'quantity' => $dto->quantity,
            'total_price' => $dto->total_price,
            'product_order_status' => $dto->product_order_status,
            'status_changed_date' => $dto->status_changed_date,
            'status_changed_by' => $dto->status_changed_by,
            'remarks' => $dto->remarks,
            'cancel_reason' => $dto->cancel_reason,
            'discount_amount' => $dto->discount_amount,

            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(OrderProductListDto $dto, $id)
    {
        $updateData = [
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'product_id' => $dto->product_id,
            'product_featured_image' => $dto->product_featured_image,
            'product_name' => $dto->product_name,
            'size' => $dto->size,
            'material' => $dto->material,
            'price' => $dto->price,
            'quantity' => $dto->quantity,
            'total_price' => $dto->total_price,
            'product_order_status' => $dto->product_order_status,
            'status_changed_date' => $dto->status_changed_date,
            'status_changed_by' => $dto->status_changed_by,
            'remarks' => $dto->remarks,
            'cancel_reason' => $dto->cancel_reason,
            'updated_by' => $dto->updated_by,
        ];

        $result = $this->repository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function updateStatus(OrderProductListDto $dto, $id)
    {
        $updateData = [
            'product_order_status' => $dto->product_order_status,
            'status_changed_date' => $dto->status_changed_date,
            'status_changed_by' => $dto->status_changed_by,
            'remarks' => $dto->remarks,
            'cancel_reason' => $dto->cancel_reason,
            'updated_by' => $dto->updated_by,
        ];

        $result = $this->repository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
