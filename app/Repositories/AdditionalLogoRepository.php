<?php

namespace App\Repositories;

use App\Models\AdditionalLogo;

class AdditionalLogoRepository
{
    public function findAll()
    {
        return AdditionalLogo::get();
    }

    public function findById($id)
    {
        return AdditionalLogo::find($id);
    }

    public function create($data)
    {
        return AdditionalLogo::create($data);
    }

    public function update($data, $id)
    {
        $result = AdditionalLogo::find($id);
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
        $result = AdditionalLogo::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
