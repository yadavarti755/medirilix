<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository
{
    public function findForPublic()
    {
        return Brand::get();
    }

    public function findAll()
    {
        return Brand::get();
    }

    public function findById($id)
    {
        return Brand::find($id);
    }

    public function create($data)
    {
        return Brand::create($data);
    }

    public function update($data, $id)
    {
        $result = Brand::find($id);
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
        $result = Brand::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
