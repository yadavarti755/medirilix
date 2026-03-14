<?php

namespace App\Services;

use App\DTO\SocialMediaDto;
use App\Repositories\SocialMediaRepository;
use App\Traits\FileUploadTraits;

class SocialMediaService
{
    use FileUploadTraits;
    private $socialMediaRepository;

    public function __construct()
    {
        $this->socialMediaRepository = new SocialMediaRepository();
    }

    public function findForPublic()
    {
        return $this->socialMediaRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->socialMediaRepository->findAll();
    }

    public function findById($id)
    {
        return $this->socialMediaRepository->findById($id);
    }

    public function create(SocialMediaDto $socialMediaDto)
    {
        return $this->socialMediaRepository->create([
            'type' => $socialMediaDto->type,
            'name' => $socialMediaDto->name,
            'url' => $socialMediaDto->url,
            'icon_class' => $socialMediaDto->icon_class,
            'created_by' => $socialMediaDto->created_by,
            'updated_by' => $socialMediaDto->updated_by,
        ]);
    }

    public function update(SocialMediaDto $socialMediaDto, $id)
    {

        $data = [
            'type' => $socialMediaDto->type,
            'name' => $socialMediaDto->name,
            'url' => $socialMediaDto->url,
            'icon_class' => $socialMediaDto->icon_class,
            'created_by' => $socialMediaDto->created_by,
            'updated_by' => $socialMediaDto->updated_by,
        ];

        return $this->socialMediaRepository->update($data, $id);
    }


    public function delete($id)
    {
        return $this->socialMediaRepository->delete($id);
    }
}
