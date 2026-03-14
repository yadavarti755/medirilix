<?php

namespace App\Repositories;

use App\Models\ProductType;

class ProductTypeRepository
{
    public function findForPublic()
    {
        return ProductType::get();
    }

    public function findAll()
    {
        return ProductType::get();
    }

    public function findById($id)
    {
        return ProductType::find($id);
    }

    public function create($data)
    {
        return ProductType::create($data);
    }

    public function update($data, $id)
    {
        $result = ProductType::find($id);
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
        $result = ProductType::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
