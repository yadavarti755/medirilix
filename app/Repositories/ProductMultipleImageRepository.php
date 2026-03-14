<?php

namespace App\Repositories;

use App\Models\ProductMultipleImage;

class ProductMultipleImageRepository
{
    public function findAll($where = [], $limit = 10)
    {
        if ($where) {
            return ProductMultipleImage::where($where)->orderBy('id', 'DESC')->limit($limit)->get();
        }
        return ProductMultipleImage::orderBy('id', 'DESC')->limit(24)->get();
    }

    public function findById($id)
    {
        return ProductMultipleImage::find($id);
    }

    public function create($data)
    {
        return ProductMultipleImage::create($data);
    }

    public function update($data, $id)
    {
        $result = ProductMultipleImage::find($id);
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
        $result = ProductMultipleImage::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function findByProduct($productId)
    {
        return ProductMultipleImage::where('product_id', $productId)->get();
    }
}
