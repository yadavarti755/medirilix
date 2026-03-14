<?php

namespace App\Repositories;

use App\Models\Salutation;

class SalutationRepository
{
    public function findAll()
    {
        return Salutation::orderBy('order', 'ASC')->get();
    }

    public function findById($id)
    {
        return Salutation::find($id);
    }

    public function create($data)
    {
        return Salutation::create($data);
    }

    public function update($data, $id)
    {
        $result = Salutation::find($id);
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
        $result = Salutation::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
