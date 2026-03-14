<?php

namespace App\Services;

use App\DTO\ContactUsDto;
use App\Repositories\ContactUsRepository;

class ContactUsService
{
    private $contactUsRepository;

    public function __construct()
    {
        $this->contactUsRepository = new ContactUsRepository();
    }

    public function create(ContactUsDto $contactUsDto)
    {
        return $this->contactUsRepository->create([
            'name' => $contactUsDto->name,
            'email_id' => $contactUsDto->email_id,
            'phone_number' => $contactUsDto->phone_number,
            'message' => $contactUsDto->message,
            'status' => $contactUsDto->status,
            'created_by' => $contactUsDto->created_by,
            'updated_by' => $contactUsDto->updated_by,
        ]);
    }
}
