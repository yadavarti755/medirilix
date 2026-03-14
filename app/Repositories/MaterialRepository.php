<?php

namespace App\Repositories;

use App\Models\Material;

class MaterialRepository
{
    public function findForPublic()
    {
        return Material::get();
    }

    public function findAll()
    {
        return Material::get();
    }

    public function findById($id)
    {
        return Material::find($id);
    }

    public function create($data)
    {
        return Material::create($data);
    }

    public function update($data, $id)
    {
        $result = Material::find($id);
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
        $result = Material::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
