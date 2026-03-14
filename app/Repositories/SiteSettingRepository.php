<?php

namespace App\Repositories;

use App\Models\SiteSetting;

class SiteSettingRepository
{
    public function findFirst()
    {
        return SiteSetting::first();
    }

    public function findById($id)
    {
        return SiteSetting::find($id);
    }

    public function update($data, $id)
    {
        $siteSetting = SiteSetting::find($id);
        if ($siteSetting) {
            $result = $siteSetting->update($data);
            if (!$result) {
                return false;
            }
            return $siteSetting;
        }
        return false;
    }
}
