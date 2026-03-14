<?php

namespace App\Services;

use App\DTO\AddressDto;
use App\Repositories\AddressRepository;

class AddressService
{
    private $addressRepository;

    public function __construct()
    {
        $this->addressRepository = new AddressRepository();
    }

    public function findAllUsersAddress()
    {
        return $this->addressRepository->findAllUsersAddress();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->addressRepository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->addressRepository->findById($id);
    }

    public function create(AddressDto $dto)
    {

        $result = $this->addressRepository->create([
            'user_id' => $dto->user_id,
            'type' => $dto->type,
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
            'status' => $dto->status,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(AddressDto $dto, $where)
    {
        $updateData = [
            'type' => $dto->type,
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
            'status' => $dto->status,
            'updated_by' => $dto->updated_by,
        ];

        $result = $this->addressRepository->update($updateData, $where);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->addressRepository->delete($id);
    }

    public function findByUserAndId($userId, $addressId)
    {
        return $this->addressRepository->findByUserAndId($userId, $addressId);
    }

    public function getSelectedAddress($userId, $addressId)
    {
        return $this->addressRepository->getSelectedAddress($userId, $addressId);
    }
}
