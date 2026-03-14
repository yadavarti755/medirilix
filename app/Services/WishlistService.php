<?php

namespace App\Services;

use App\DTO\WishlistDto;
use App\Repositories\WishlistRepository;

class WishlistService
{
    private $wishlistRepository;

    public function __construct()
    {
        $this->wishlistRepository = new WishlistRepository();
    }

    public function findAll($where = [])
    {
        return $this->wishlistRepository->findAll($where);
    }

    public function findByUser($userId)
    {
        return $this->wishlistRepository->findByUser($userId);
    }

    public function findById($id)
    {
        return $this->wishlistRepository->findById($id);
    }

    public function create(WishlistDto $wishlistDto)
    {

        $result = $this->wishlistRepository->create([
            'user_id' => $wishlistDto->user_id,
            'product_id' => $wishlistDto->product_id,
            'created_by' => $wishlistDto->created_by,
            'updated_by' => $wishlistDto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(WishlistDto $wishlistDto, $id)
    {
        $updateData = [
            'user_id' => $wishlistDto->user_id,
            'product_id' => $wishlistDto->product_id,
            'updated_by' => $wishlistDto->updated_by,
        ];

        $result = $this->wishlistRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->wishlistRepository->delete($id);
    }
}
