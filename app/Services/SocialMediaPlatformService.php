<?php

namespace App\Services;

use App\Repositories\SocialMediaPlatformRepository;
use App\Traits\FileUploadTraits;

class SocialMediaPlatformService
{
    private $socialMediaPlatformRepository;

    public function __construct()
    {
        $this->socialMediaPlatformRepository = new SocialMediaPlatformRepository();
    }

    public function findAll()
    {
        return $this->socialMediaPlatformRepository->findAll();
    }

    public function findById($id)
    {
        return $this->socialMediaPlatformRepository->findById($id);
    }
}
