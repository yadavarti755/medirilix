<?php

namespace App\Services;

use App\Repositories\MenuLocationRepository;

class MenuLocationService
{
    private $menuLocationRepository;

    public function __construct()
    {
        $this->menuLocationRepository = new MenuLocationRepository();
    }

    public function findAll()
    {
        return $this->menuLocationRepository->findAll();
    }

    public function findById($id)
    {
        return $this->menuLocationRepository->findById($id);
    }

    public function findByCode($code)
    {
        return $this->menuLocationRepository->findByCode($code);
    }

    public function delete($id)
    {
        return $this->menuLocationRepository->delete($id);
    }
}
