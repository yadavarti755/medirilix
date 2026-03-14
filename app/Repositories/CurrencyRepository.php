<?php

namespace App\Repositories;

use App\Models\Currency;

class CurrencyRepository
{
    public function findAll()
    {
        return Currency::get();
    }

    public function findById($id)
    {
        return Currency::find($id);
    }

    public function create($data)
    {
        return Currency::create($data);
    }

    public function update($data, $id)
    {
        $result = Currency::find($id);
        if ($result) {
            $result->update($data);
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Currency::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
