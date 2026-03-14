<?php

namespace App\Services;

use App\DTO\SizeDto;
use App\Repositories\SizeRepository;

class SizeService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new SizeRepository();
    }

    public function findForPublic()
    {
        return $this->repository->findForPublic();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(SizeDto $dto)
    {
        $result = $this->repository->create([
            'name' => $dto->name,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(SizeDto $dto, $id)
    {
        $updateData = [
            'name' => $dto->name,
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
