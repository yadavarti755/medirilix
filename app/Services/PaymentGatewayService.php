<?php

namespace App\Services;

use App\Repositories\PaymentGatewayRepository;
use App\Traits\FileUploadTraits;
use App\DTO\PaymentGatewayDto;
use Illuminate\Support\Facades\Config;

class PaymentGatewayService
{
    use FileUploadTraits;
    protected $paymentGatewayRepository;

    public function __construct(PaymentGatewayRepository $paymentGatewayRepository)
    {
        $this->paymentGatewayRepository = $paymentGatewayRepository;
    }

    public function findAll()
    {
        return $this->paymentGatewayRepository->findAll();
    }

    public function findActive()
    {
        return $this->paymentGatewayRepository->findActive();
    }

    public function findById($id)
    {
        return $this->paymentGatewayRepository->findById($id);
    }

    public function create(PaymentGatewayDto $dto)
    {
        if ($dto->image) {
            $file = $this->uploadFile($dto->image, Config::get('file_paths')['PAYMENT_GATEWAY_IMAGE_PATH']);
            $dto->image = $file['file_name'];
        }

        $creationData = [
            'gateway_name' => $dto->gateway_name,
            'app_id' => $dto->app_id,
            'client_id_or_key' => $dto->client_id_or_key,
            'client_secret' => $dto->client_secret,
            'is_active' => $dto->is_active,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->image) {
            $creationData['file_name'] = $dto->image;
        }

        return $this->paymentGatewayRepository->create($creationData);
    }

    public function update(PaymentGatewayDto $dto, $id)
    {
        if ($dto->image) {
            $file = $this->uploadFile($dto->image, Config::get('file_paths')['PAYMENT_GATEWAY_IMAGE_PATH']);
            $dto->image = $file['file_name'];
        }

        $updateData = [
            'gateway_name' => $dto->gateway_name,
            'app_id' => $dto->app_id,
            'client_id_or_key' => $dto->client_id_or_key,
            'client_secret' => $dto->client_secret,
            'is_active' => $dto->is_active,
            'updated_by' => $dto->updated_by,
        ];

        if ($dto->image) {
            $updateData['file_name'] = $dto->image;
        }

        return $this->paymentGatewayRepository->update($updateData, $id);
    }

    public function delete($id)
    {
        return $this->paymentGatewayRepository->delete($id);
    }
}
