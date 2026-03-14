<?php

namespace App\Services;

use App\Repositories\SalutationRepository;

class SalutationService
{
    private $salutationRepository;

    public function __construct()
    {
        $this->salutationRepository = new SalutationRepository();
    }

    public function findAll()
    {
        return $this->salutationRepository->findAll();
    }

    public function findById($id)
    {
        return $this->salutationRepository->findById($id);
    }

    public function delete($id)
    {
        return $this->salutationRepository->delete($id);
    }
}
