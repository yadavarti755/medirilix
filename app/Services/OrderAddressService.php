<?php

namespace App\Services;

use App\DTO\OrderAddressDto;
use App\Repositories\OrderAddressRepository;

class OrderAddressService
{
    private $orderAddressRepository;

    public function __construct()
    {
        $this->orderAddressRepository = new OrderAddressRepository();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->orderAddressRepository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->orderAddressRepository->findById($id);
    }

    public function create(OrderAddressDto $dto)
    {
        $result = $this->orderAddressRepository->create([
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'person_name' => $dto->person_name,
            'person_contact_number' => $dto->person_contact_number,
            'person_alt_contact_number' => $dto->person_alt_contact_number,
            'address' => $dto->address,
            'locality' => $dto->locality,
            'landmark' => $dto->landmark,
            'city' => $dto->city,
            'state' => $dto->state,
            'country' => $dto->country,
            'pincode' => $dto->pincode,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(OrderAddressDto $dto, $id)
    {
        $updateData = [
            'user_id' => $dto->user_id,
            'order_number' => $dto->order_number,
            'person_name' => $dto->person_name,
            'person_contact_number' => $dto->person_contact_number,
            'person_alt_contact_number' => $dto->person_alt_contact_number,
            'address' => $dto->address,
            'locality' => $dto->locality,
            'landmark' => $dto->landmark,
            'city' => $dto->city,
            'state' => $dto->state,
            'country' => $dto->country,
            'pincode' => $dto->pincode,
            'updated_by' => $dto->updated_by,
        ];

        $result = $this->orderAddressRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->orderAddressRepository->delete($id);
    }

    public function getSelectedAddress($userId, $addressId)
    {
        return $this->orderAddressRepository->findOne([
            'user_id' => $userId,
            'id' => $addressId
        ]);
    }
}
