<?php

namespace App\Services;

use App\DTO\OrderHistoryDto;
use App\Repositories\OrderHistoryRepository;

class OrderHistoryService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new OrderHistoryRepository();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->repository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(OrderHistoryDto $dto)
    {
        $result = $this->repository->create([
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'order_status' => $dto->order_status,
            'status_changed_date' => $dto->status_changed_date,
            'remarks' => $dto->remarks,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(OrderHistoryDto $dto, $id)
    {
        $updateData = [
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'order_status' => $dto->order_status,
            'status_changed_date' => $dto->status_changed_date,
            'remarks' => $dto->remarks,
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
