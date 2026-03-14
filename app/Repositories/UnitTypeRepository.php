<?php

namespace App\Repositories;

use App\Models\UnitType;

class UnitTypeRepository
{
    public function findForPublic()
    {
        return UnitType::get();
    }

    public function findAll()
    {
        return UnitType::get();
    }

    public function findById($id)
    {
        return UnitType::find($id);
    }

    public function create($data)
    {
        return UnitType::create($data);
    }

    public function update($data, $id)
    {
        $result = UnitType::find($id);
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
        $result = UnitType::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
