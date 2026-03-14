<?php

namespace App\Services;

use App\DTO\ProductMultipleImageDto;
use App\Repositories\ProductMultipleImageRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class ProductMultipleImageService
{
    use FileUploadTraits;
    private $productMultipleImageRepository;

    public function __construct()
    {
        $this->productMultipleImageRepository = new ProductMultipleImageRepository();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->productMultipleImageRepository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->productMultipleImageRepository->findById($id);
    }

    public function create(ProductMultipleImageDto $dto)
    {
        if ($dto->image_name && $dto->image_name != 'no-image.png') {
            $file = $this->uploadFile($dto->image_name, Config::get('file_paths')['PRODUCT_MULTIPLE_IMAGE_PATH']);
            $dto->image_name = $file['file_name'];
        }

        $result = $this->productMultipleImageRepository->create([
            'product_id' => $dto->product_id,
            'image_name' => $dto->image_name,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function update(ProductMultipleImageDto $dto, $id)
    {
        // Typically image updates might just replace the file or delete/re-upload
        // For simplicity, if a new image is provided, we update it
        $data = [
            'product_id' => $dto->product_id,
            'image_name' => $dto->image_name,
            'updated_by' => $dto->updated_by,
            'updated_at' => now(),
        ];

        if ($dto->image_name && $dto->image_name != 'no-image.png') {
            $file = $this->uploadFile($dto->image_name, Config::get('file_paths')['PRODUCT_MULTIPLE_IMAGE_PATH']);
            $data['image_name'] = $file['file_name'];
        }

        $result = $this->productMultipleImageRepository->update($data, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        // Optionally delete the file from storage here
        return $this->productMultipleImageRepository->delete($id);
    }

    public function findByProduct($productId)
    {
        return $this->productMultipleImageRepository->findByProduct($productId);
    }
}
