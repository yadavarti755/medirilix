<?php

namespace App\Services;

use App\DTO\BrandDto;
use App\Traits\FileUploadTraits;
use App\Repositories\BrandRepository;
use Illuminate\Support\Facades\Config;

class BrandService
{
    use FileUploadTraits;
    private $repository;

    public function __construct()
    {
        $this->repository = new BrandRepository();
    }

    public function findForPublic()
    {
        return $this->repository->findForPublic();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(BrandDto $dto)
    {
        if ($dto->file_name) {
            $file = $this->uploadFile($dto->file_name, Config::get('file_paths')['BRAND_IMAGE_PATH']);
            $dto->file_name = $file['file_name'];
        }

        $creationData = [
            'name' => $dto->name,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->file_name) {
            $creationData['file_name'] = $dto->file_name;
        }

        $result = $this->repository->create($creationData);


        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(BrandDto $dto, $id)
    {
        if ($dto->file_name) {
            $file = $this->uploadFile($dto->file_name, Config::get('file_paths')['BRAND_IMAGE_PATH']);
            $dto->file_name = $file['file_name'];
        }

        $updateData = [
            'name' => $dto->name,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->file_name) {
            $updateData['file_name'] = $dto->file_name;
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
