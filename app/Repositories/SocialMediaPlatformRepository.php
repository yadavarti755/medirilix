<?php

namespace App\Repositories;

use App\Models\SocialMediaPlatform;

class SocialMediaPlatformRepository
{
    public function findAll()
    {
        return SocialMediaPlatform::orderBy('name', 'ASC')->get();
    }

    public function findById($id)
    {
        return SocialMediaPlatform::find($id);
    }

    public function create($data)
    {
        return SocialMediaPlatform::create($data);
    }

    public function update($data, $id)
    {
        $result = SocialMediaPlatform::find($id);
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
        $result = SocialMediaPlatform::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
