<?php

namespace App\Services;

use App\Repositories\AuthenticationLogRepository;

class AuthenticationLogService
{
    private $authenticationLogRepository;

    public function __construct()
    {
        $this->authenticationLogRepository = new AuthenticationLogRepository();
    }

    public function findAll()
    {
        return $this->authenticationLogRepository->findAll();
    }

    public function findAllForDatatable()
    {
        return $this->authenticationLogRepository->findAllForDatatable();
    }
}
