<?php

namespace App\Services;

use App\Repositories\MenuRepository;

class MenuService
{
    private $menuRepository;

    public function __construct()
    {
        $this->menuRepository = new MenuRepository();
    }

    public function findAll()
    {
        return $this->menuRepository->findAll();
    }

    public function findAllExcluding($id)
    {
        return $this->menuRepository->findAllExcluding($id);
    }

    public function findById($id)
    {
        return $this->menuRepository->findById($id);
    }

    public function findForIndex($locationCode)
    {
        return $this->menuRepository->findForIndex($locationCode);
    }

    public function create($dto)
    {
        return $this->menuRepository->create([
            'title' => $dto->title,
            'url' => $dto->url,
            'parent_id' => $dto->parent_id,
            'order' => $dto->order,
            'location' => $dto->location,
            'permission_name' => $dto->permission_name,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);
    }

    public function update($dto, $id)
    {
        return $this->menuRepository->update([
            'title' => $dto->title,
            'url' => $dto->url,
            'parent_id' => $dto->parent_id,
            'order' => $dto->order,
            'location' => $dto->location,
            'permission_name' => $dto->permission_name,
            'updated_by' => $dto->updated_by,
        ], $id);
    }

    public function updateOrder(array $items, $parentId)
    {
        foreach ($items as $index => $item) {
            $this->menuRepository->update([
                'parent_id' => $parentId,
                'order' => $index,
                'updated_by' => auth()->user()->id,
            ], $item['id']);

            if (isset($item['children'])) {
                $this->updateOrder($item['children'], $item['id']); // Recursion works via this service method now
            }
        }
    }
    public function delete($id)
    {
        return $this->menuRepository->delete($id);
    }

    public function findByUrlWithParents($url)
    {
        return $this->menuRepository->findByUrlWithParents($url);
    }

    public function fetchMainParentName($parents)
    {
        // Logic to fetch main parent name from parents collection
        if ($parents->isNotEmpty()) {
            return $parents->last()->title;
        }
        return '';
    }

    public function fetchMainParent($parents)
    {
        // Logic to fetch main parent object from parents collection
        if ($parents->isNotEmpty()) {
            return $parents->last();
        }
        return null;
    }

    public function findAllById($id)
    {
        return $this->menuRepository->findAllById($id);
    }
}
