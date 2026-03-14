<?php

namespace App\Services;

use App\DTO\PaymentMethodDto;
use App\Repositories\PaymentMethodRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class PaymentMethodService
{
    use FileUploadTraits;
    private $paymentMethodRepository;

    public function __construct()
    {
        $this->paymentMethodRepository = new PaymentMethodRepository();
    }

    public function findForPublic()
    {
        return $this->paymentMethodRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->paymentMethodRepository->findAll();
    }

    public function findById($id)
    {
        return $this->paymentMethodRepository->findById($id);
    }

    public function create(PaymentMethodDto $paymentMethodDto)
    {
        // Upload image
        if ($paymentMethodDto->image) {
            $file = $this->uploadFile($paymentMethodDto->image, Config::get('file_paths')['PAYMENT_METHOD_IMAGE_PATH']);
            $paymentMethodDto->image = $file['file_name'];
        }

        $result = $this->paymentMethodRepository->create([
            'title' => $paymentMethodDto->title,
            'image' => $paymentMethodDto->image,
            'is_published' => $paymentMethodDto->is_published,
            'created_by' => $paymentMethodDto->created_by,
            'updated_by' => $paymentMethodDto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function update(PaymentMethodDto $paymentMethodDto, $id)
    {
        // Upload image if new one provided
        if ($paymentMethodDto->image) {
            $file = $this->uploadFile($paymentMethodDto->image, Config::get('file_paths')['PAYMENT_METHOD_IMAGE_PATH']);
            $paymentMethodDto->image = $file['file_name'];
        }

        $updateData = [
            'title' => $paymentMethodDto->title,
            'is_published' => $paymentMethodDto->is_published,
            'updated_by' => $paymentMethodDto->updated_by,
        ];

        if ($paymentMethodDto->image) {
            $updateData['image'] = $paymentMethodDto->image;
        }

        $result = $this->paymentMethodRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->paymentMethodRepository->delete($id);
    }

    public function publish(PaymentMethodDto $paymentMethodDto, $id)
    {
        $updateData = [
            'is_published' => $paymentMethodDto->is_published,
            'updated_by' => $paymentMethodDto->updated_by,
        ];

        $result = $this->paymentMethodRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }
}
