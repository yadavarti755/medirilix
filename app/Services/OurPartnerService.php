<?php

namespace App\Services;

use App\DTO\OurPartnerDto;
use App\Repositories\OurPartnerRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class OurPartnerService
{
    use FileUploadTraits;
    private $ourPartnerRepository;

    public function __construct()
    {
        $this->ourPartnerRepository = new OurPartnerRepository();
    }

    public function findForPublic()
    {
        return $this->ourPartnerRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->ourPartnerRepository->findAll();
    }

    public function findById($id)
    {
        return $this->ourPartnerRepository->findById($id);
    }

    public function create(OurPartnerDto $ourPartnerDto)
    {
        // Upload header logo
        if ($ourPartnerDto->file_name) {
            $headerFile = $this->uploadFile($ourPartnerDto->file_name, Config::get('file_paths')['OUR_PARTNER_IMAGE_PATH']);
            $ourPartnerDto->file_name = $headerFile['file_name'];
        }

        $result = $this->ourPartnerRepository->create([
            'file_name' => $ourPartnerDto->file_name,
            'title' => $ourPartnerDto->title,
            'link' => $ourPartnerDto->link,
            'created_by' => $ourPartnerDto->created_by,
            'updated_by' => $ourPartnerDto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(OurPartnerDto $ourPartnerDto, $id)
    {
        // Upload header logo
        if ($ourPartnerDto->file_name) {
            $headerFile = $this->uploadFile($ourPartnerDto->file_name, Config::get('file_paths')['OUR_PARTNER_IMAGE_PATH']);
            $ourPartnerDto->file_name = $headerFile['file_name'];
        }

        $updateData = [
            'title' => $ourPartnerDto->title,
            'link' => $ourPartnerDto->link,
            'created_by' => $ourPartnerDto->created_by,
            'updated_by' => $ourPartnerDto->updated_by,
        ];

        if ($ourPartnerDto->file_name) {
            $updateData['file_name'] = $ourPartnerDto->file_name;
        }

        $result = $this->ourPartnerRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->ourPartnerRepository->delete($id);
    }
}
