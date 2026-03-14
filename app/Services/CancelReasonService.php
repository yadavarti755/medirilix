<?php

namespace App\Services;

use App\DTO\CancelReasonDto;
use App\Repositories\CancelReasonRepository;

class CancelReasonService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new CancelReasonRepository();
    }

    public function create(CancelReasonDto $dto)
    {
        return $this->repository->create([
            'title' => $dto->title,
            'created_by' => $dto->created_by,
        ]);
    }

    public function update(CancelReasonDto $dto, $id)
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
