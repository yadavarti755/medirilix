<?php

namespace App\Repositories;

use App\Models\ProductOtherSpecification;

class ProductOtherSpecificationRepository
{
    public function create(array $data)
    {
        return ProductOtherSpecification::create($data);
    }

    public function update(array $data, $id)
    {
        $spec = ProductOtherSpecification::find($id);
        if ($spec) {
            $spec->update($data);
            return $spec;
        }
        return null;
    }

    public function delete($id)
    {
        $spec = ProductOtherSpecification::find($id);
        if ($spec) {
            return $spec->delete();
        }
        return false;
    }

    public function deleteByProductId($productId)
    {
        return ProductOtherSpecification::where('product_id', $productId)->delete();
    }

    public function findByProductId($productId)
    {
        return ProductOtherSpecification::where('product_id', $productId)->get();
    }

    public function findById($id)
    {
        return ProductOtherSpecification::find($id);
    }
}
