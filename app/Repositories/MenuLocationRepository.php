<?php

namespace App\Repositories;

use App\Models\MenuLocation;

class MenuLocationRepository
{
    public function findAll()
    {
        return MenuLocation::get();
    }

    public function findById($id)
    {
        return MenuLocation::find($id);
    }

    public function findByCode($code)
    {
        return MenuLocation::where([
            'location_code' => $code
        ])->first();
    }

    public function create($data)
    {
        return MenuLocation::create($data);
    }

    public function update($data, $id)
    {
        $menu = MenuLocation::find($id);
        if ($menu) {
            $result = $menu->update($data);
            if (!$result) {
                return false;
            }
            return $menu;
        }
        return false;
    }

    public function delete($id)
    {
        $menu = MenuLocation::find($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }
}
