<?php

namespace App\Repositories;

use App\Models\IntendedUse;

class IntendedUseRepository
{
    public function findForPublic()
    {
        return IntendedUse::get();
    }

    public function findAll()
    {
        return IntendedUse::get();
    }

    public function findById($id)
    {
        return IntendedUse::find($id);
    }

    public function create($data)
    {
        return IntendedUse::create($data);
    }

    public function update($data, $id)
    {
        $result = IntendedUse::find($id);
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
        $result = IntendedUse::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
