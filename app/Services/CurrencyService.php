<?php

namespace App\Services;

use App\DTO\CurrencyDto;
use App\Repositories\CurrencyRepository;

class CurrencyService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new CurrencyRepository();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(CurrencyDto $dto)
    {
        return $this->repository->create([
            'currency' => $dto->currency,
            'symbol' => $dto->symbol,
            'amount_in_dollars' => $dto->amount_in_dollars,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);
    }

    public function update(CurrencyDto $dto, $id)
    {
        $updateData = [
            'currency' => $dto->currency,
            'symbol' => $dto->symbol,
            'amount_in_dollars' => $dto->amount_in_dollars,
            'updated_by' => $dto->updated_by,
        ];
        return $this->repository->update($updateData, $id);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
