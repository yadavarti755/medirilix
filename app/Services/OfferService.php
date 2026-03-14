<?php

namespace App\Services;

use App\DTO\OfferDto;
use App\Traits\FileUploadTraits;
use App\Repositories\OfferRepository;
use Illuminate\Support\Facades\Config;

class OfferService
{
    use FileUploadTraits;
    private $repository;

    public function __construct()
    {
        $this->repository = new OfferRepository();
    }

    public function findForPublic($where = [])
    {
        return $this->repository->findForPublic($where);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(OfferDto $dto)
    {
        if ($dto->image) {
            $file = $this->uploadFile($dto->image, Config::get('file_paths')['OFFER_IMAGE_PATH']);
            $dto->image = $file['file_name'];
        }

        $creationData = [
            'title' => $dto->title,
            'description' => $dto->description,
            'type' => $dto->type,
            'type_id' => $dto->type_id,
            'is_active' => $dto->is_active,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->image) {
            $creationData['image'] = $dto->image;
        }

        $result = $this->repository->create($creationData);


        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(OfferDto $dto, $id)
    {
        if ($dto->image) {
            $file = $this->uploadFile($dto->image, Config::get('file_paths')['OFFER_IMAGE_PATH']);
            $dto->image = $file['file_name'];
        }

        $updateData = [
            'title' => $dto->title,
            'description' => $dto->description,
            'type' => $dto->type,
            'type_id' => $dto->type_id,
            'is_active' => $dto->is_active,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->image) {
            $updateData['image'] = $dto->image;
        }

        $result = $this->repository->update($updateData, $id);
        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
