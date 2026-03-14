<?php

namespace App\Services;

use App\DTO\ReturnPolicyDto;
use App\Repositories\ReturnPolicyRepository;

class ReturnPolicyService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new ReturnPolicyRepository();
    }

    public function create(ReturnPolicyDto $dto)
    {
        return $this->repository->create([
            'title' => $dto->title,
            'return_till_days' => $dto->return_till_days,
            'return_description' => $dto->return_description,
            'created_by' => $dto->created_by,
        ]);
    }

    public function update(ReturnPolicyDto $dto, $id)
    {
        return $this->repository->update([
            'title' => $dto->title,
            'return_till_days' => $dto->return_till_days,
            'return_description' => $dto->return_description,
            'updated_by' => $dto->updated_by,
        ], $id);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function findForDatatable()
    {
        return $this->repository->findForDatatable();
    }
}
