<?php

namespace App\Services;

use App\DTO\StateDto;
use App\Repositories\StateRepository;

class StateService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new StateRepository();
    }

    public function findForPublic($where = [])
    {
        return $this->repository->findForPublic($where);
    }

    public function findAll($where = [])
    {
        return $this->repository->findAll($where);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(StateDto $dto)
    {
        $result = $this->repository->create([
            'country_id' => $dto->country_id,
            'name'       => $dto->name,
            'iso2'       => $dto->iso2,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(StateDto $dto, $id)
    {
        $updateData = [
            'country_id' => $dto->country_id,
            'name'       => $dto->name,
            'iso2'       => $dto->iso2,
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
