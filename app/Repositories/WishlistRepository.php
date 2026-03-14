<?php

namespace App\Repositories;

use App\Models\Wishlist;

class WishlistRepository
{
    public function findAll($where = [])
    {
        if ($where) {
            return Wishlist::where($where)->orderBy('id', 'desc')->get();
        }
        return Wishlist::orderBy('id', 'desc')->get();
    }

    public function findByUser($userId)
    {
        return Wishlist::with('product')->where('user_id', $userId)->orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return Wishlist::find($id);
    }

    public function create($data)
    {
        return Wishlist::create($data);
    }

    public function update($data, $id)
    {
        $result = Wishlist::find($id);
        if ($result) {
            $result = $result->update($data);
            if (!$result) {
                return false;
            }
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Wishlist::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
