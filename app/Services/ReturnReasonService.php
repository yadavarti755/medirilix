<?php

namespace App\Services;

use App\DTO\ReturnReasonDto;
use App\Repositories\ReturnReasonRepository;

class ReturnReasonService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new ReturnReasonRepository();
    }

    public function create(ReturnReasonDto $dto)
    {
        return $this->repository->create([
            'title' => $dto->title,
            'created_by' => $dto->created_by,
        ]);
    }

    public function update(ReturnReasonDto $dto, $id)
    {
        return $this->repository->update([
            'title' => $dto->title,
            'updated_by' => $dto->updated_by,
        ], $id);
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
