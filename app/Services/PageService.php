<?php

namespace App\Services;

use App\Repositories\PageRepository;
use App\DTO\PageDto;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class PageService
{
    use FileUploadTraits;
    private $pageRepository;

    public function __construct()
    {
        $this->pageRepository = new PageRepository();
    }

    public function findAllForDatatable()
    {
        return $this->pageRepository->findAllForDatatable();
    }

    public function findAll()
    {
        return $this->pageRepository->findAll();
    }

    public function findById($id)
    {
        return $this->pageRepository->findById($id);
    }

    public function create(PageDto $pageDto)
    {
        $page = $this->pageRepository->create([
            'menu_id' => $pageDto->menu_id,
            'title' => $pageDto->title,
            'content' => $pageDto->content,
            'is_published' => $pageDto->is_published,
            'created_by' => $pageDto->created_by,
            'updated_by' => $pageDto->updated_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$page) {
            return false;
        }

        return $page;
    }

    public function update(PageDto $pageDto, $id)
    {
        $page = $this->pageRepository->update([
            'menu_id' => $pageDto->menu_id,
            'title' => $pageDto->title,
            'content' => $pageDto->content,
            'is_published' => $pageDto->is_published,
            'updated_by' => $pageDto->updated_by,
            'updated_at' => now(),
        ], $id);

        if (!$page) {
            return false;
        }

        return $this->findById($id);
    }

    public function delete($id)
    {
        return $this->pageRepository->delete($id);
    }

    public function publish(PageDto $pageDto, $id)
    {
        $updateData = [
            'is_published' => $pageDto->is_published,
            'updated_by' => $pageDto->updated_by,
        ];

        $result = $this->pageRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function search(array $fields, string $term)
    {
        return $this->pageRepository->search($fields, $term);
    }
}
