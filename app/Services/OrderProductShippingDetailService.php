<?php

namespace App\Services;

use App\Repositories\OrderProductShippingDetailRepository;
use App\DTO\OrderProductShippingDetailDto;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class OrderProductShippingDetailService
{
    use FileUploadTraits;
    private $repository;

    public function __construct()
    {
        $this->repository = new OrderProductShippingDetailRepository();
    }

    public function findByOrderProductListId($id)
    {
        return $this->repository->findByOrderProductListId($id);
    }

    public function create(OrderProductShippingDetailDto $dto)
    {
        // Upload
        if ($dto->shipment_photos) {
            $file = $this->uploadFile($dto->shipment_photos, Config::get('file_paths')['SHIPMENT_IMAGE_PATH']);
            $dto->shipment_photos = $file['file_name'];
        }

        $result = $this->repository->create([
            'order_product_list_id' => $dto->order_product_list_id,
            'shipment_photo' => $dto->shipment_photos,
            'shipping_details' => $dto->shipping_details,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function update(OrderProductShippingDetailDto $dto, $id)
    {
        // Upload
        if ($dto->shipment_photos) {
            $file = $this->uploadFile($dto->shipment_photos, Config::get('file_paths')['SHIPMENT_IMAGE_PATH']);
            $dto->shipment_photos = $file['file_name'];
        }

        $updateData = [
            'shipping_details' => $dto->shipping_details,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->shipment_photos) {
            $updateData['shipment_photo'] = $dto->shipment_photos;
        }

        $result = $this->repository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }
}
