<?php

namespace App\Services;

use App\DTO\CountryDto;
use App\Repositories\CountryRepository;

class CountryService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new CountryRepository();
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

    public function create(CountryDto $dto)
    {
        $result = $this->repository->create([
            'name'       => $dto->name,
            'iso2'       => $dto->iso2,
            'phone_code' => $dto->phone_code,
            'currency'   => $dto->currency,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(CountryDto $dto, $id)
    {
        $updateData = [
            'name'       => $dto->name,
            'iso2'       => $dto->iso2,
            'phone_code' => $dto->phone_code,
            'currency'   => $dto->currency,
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
