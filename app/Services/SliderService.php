<?php

namespace App\Services;

use App\DTO\SliderDto;
use App\Repositories\SliderRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class SliderService
{
    use FileUploadTraits;
    private $sliderRepository;

    public function __construct()
    {
        $this->sliderRepository = new SliderRepository();
    }

    public function findForPublic()
    {
        return $this->sliderRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->sliderRepository->findAll();
    }

    public function findById($id)
    {
        return $this->sliderRepository->findById($id);
    }

    public function create(SliderDto $sliderDto)
    {
        // Upload
        if ($sliderDto->file_name) {
            $file = $this->uploadFile($sliderDto->file_name, Config::get('file_paths')['SLIDER_IMAGE_PATH']);
            $sliderDto->file_name = $file['file_name'];
        }

        $result = $this->sliderRepository->create([
            'category_id' => $sliderDto->category_id,
            'title' => $sliderDto->title,
            'subtitle' => $sliderDto->subtitle,
            'description' => $sliderDto->description,
            'file_name' => $sliderDto->file_name,
            'created_by' => $sliderDto->created_by,
            'updated_by' => $sliderDto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(SliderDto $sliderDto, $id)
    {
        // Upload
        if ($sliderDto->file_name) {
            $file = $this->uploadFile($sliderDto->file_name, Config::get('file_paths')['SLIDER_IMAGE_PATH']);
            $sliderDto->file_name = $file['file_name'];
        }

        $updateData = [
            'category_id' => $sliderDto->category_id,
            'title' => $sliderDto->title,
            'subtitle' => $sliderDto->subtitle,
            'description' => $sliderDto->description,
            'created_by' => $sliderDto->created_by,
            'updated_by' => $sliderDto->updated_by,
        ];

        if ($sliderDto->file_name) {
            $updateData['file_name'] = $sliderDto->file_name;
        }

        $result = $this->sliderRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->sliderRepository->delete($id);
    }

    public function publish(SliderDto $sliderDto, $id)
    {
        $updateData = [
            'is_published' => $sliderDto->is_published,
            'updated_by' => $sliderDto->updated_by,
        ];

        $result = $this->sliderRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }
}
