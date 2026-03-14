<?php

namespace App\Services;

use App\DTO\CategoryDto;
use App\Repositories\CategoryRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class CategoryService
{
    use FileUploadTraits;
    private $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    public function findForPublic()
    {
        return $this->categoryRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->categoryRepository->findAll();
    }

    public function findForBackend()
    {
        return $this->categoryRepository->findForBackend();
    }

    public function findAllForEdit($id)
    {
        return $this->categoryRepository->findAllForEdit($id);
    }

    public function findById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function create(CategoryDto $categoryDto)
    {
        if ($categoryDto->image) {
            $file = $this->uploadFile($categoryDto->image, Config::get('file_paths')['CATEGORY_IMAGE_PATH']);
            $categoryDto->image = $file['file_name'];
        }

        $result = $this->categoryRepository->create([
            'name' => $categoryDto->name,
            'description' => $categoryDto->description,
            'image' => $categoryDto->image,
            'parent_id' => $categoryDto->parent_id,
            'order' => $categoryDto->order,
            'is_published' => $categoryDto->is_published,
            'created_by' => $categoryDto->created_by,
            'updated_by' => $categoryDto->updated_by,
        ]);

        return $result;
    }

    public function update(CategoryDto $categoryDto, $id)
    {
        if ($categoryDto->image) {
            $file = $this->uploadFile($categoryDto->image, Config::get('file_paths')['CATEGORY_IMAGE_PATH']);
            $categoryDto->image = $file['file_name'];
        }

        $updateData = [
            'name' => $categoryDto->name,
            'description' => $categoryDto->description,
            'parent_id' => $categoryDto->parent_id,
            'order' => $categoryDto->order,
            'is_published' => $categoryDto->is_published,
            'updated_by' => $categoryDto->updated_by,
        ];

        if ($categoryDto->image) {
            $updateData['image'] = $categoryDto->image;
        }

        return $this->categoryRepository->update($updateData, $id);
    }

    public function delete($id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function publish(CategoryDto $categoryDto, $id)
    {
        $updateData = [
            'is_published' => $categoryDto->is_published,
            'updated_by' => $categoryDto->updated_by,
        ];

        $result = $this->categoryRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function findBySlug($slug)
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function searchByName($query, $limit = 4)
    {
        return $this->categoryRepository->searchByName($query, $limit);
    }

    public function updateOrder($data, $id)
    {
        return $this->categoryRepository->update($data, $id);
    }
}
