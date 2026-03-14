<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository
{
    public function findForPublic()
    {
        return Country::get();
    }

    public function findAll()
    {
        return Country::get();
    }

    public function findById($id)
    {
        return Country::find($id);
    }

    public function create($data)
    {
        return Country::create($data);
    }

    public function update($data, $id)
    {
        $result = Country::find($id);
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
        $result = Country::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
