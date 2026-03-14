<?php

namespace App\Services;

use App\DTO\ProductOtherSpecificationDto;
use App\Repositories\ProductOtherSpecificationRepository;

class ProductOtherSpecificationService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductOtherSpecificationRepository();
    }

    public function create(ProductOtherSpecificationDto $dto)
    {
        return $this->repository->create([
            'product_id' => $dto->product_id,
            'label' => $dto->label,
            'value' => $dto->value,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);
    }

    public function update(ProductOtherSpecificationDto $dto, $id)
    {
        return $this->repository->update([
            'product_id' => $dto->product_id, // Usually not updated, but good to keep consistent
            'label' => $dto->label,
            'value' => $dto->value,
            'updated_by' => $dto->updated_by,
        ], $id);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function deleteByProductId($productId)
    {
        return $this->repository->deleteByProductId($productId);
    }

    public function findByProductId($productId)
    {
        return $this->repository->findByProductId($productId);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }
}
