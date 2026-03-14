<?php

namespace App\Services;

use App\Repositories\SubscribeNewsletterRepository;
use Illuminate\Support\Facades\Log;

class SubscribeNewsletterService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new SubscribeNewsletterRepository();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create($dto)
    {
        // Check if email already exists
        // Assuming unique constraint might not be on DB level or we want soft check? 
        // Logic says just create. But good to check.
        // For now, standard create.
        return $this->repository->create((array) $dto);
    }

    public function delete($id)
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('SubscribeNewsletter Deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
