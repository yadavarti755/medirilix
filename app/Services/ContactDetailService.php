<?php

namespace App\Services;

use App\DTO\ContactDetailDto;
use App\Repositories\ContactDetailRepository;
use App\Traits\FileUploadTraits;

class ContactDetailService
{
    use FileUploadTraits;
    private $contactDetailRepository;

    public function __construct()
    {
        $this->contactDetailRepository = new ContactDetailRepository();
    }

    public function findAll()
    {
        return $this->contactDetailRepository->findAll();
    }

    public function findById($id)
    {
        return $this->contactDetailRepository->findById($id);
    }

    public function create(ContactDetailDto $contactDetailDto)
    {
        return $this->contactDetailRepository->create([
            'address' => $contactDetailDto->address,
            'phone_numbers' => $contactDetailDto->phone_numbers,
            'email_ids' => $contactDetailDto->email_ids,
            'is_primary' => $contactDetailDto->is_primary,
            'created_by' => $contactDetailDto->created_by,
            'updated_by' => $contactDetailDto->updated_by,
        ]);
    }

    public function update(ContactDetailDto $contactDetailDto, $id)
    {

        $data = [
            'address' => $contactDetailDto->address,
            'phone_numbers' => $contactDetailDto->phone_numbers,
            'email_ids' => $contactDetailDto->email_ids,
            'is_primary' => $contactDetailDto->is_primary,
            'created_by' => $contactDetailDto->created_by,
            'updated_by' => $contactDetailDto->updated_by,
        ];

        return $this->contactDetailRepository->update($data, $id);
    }


    public function delete($id)
    {
        return $this->contactDetailRepository->delete($id);
    }
}
