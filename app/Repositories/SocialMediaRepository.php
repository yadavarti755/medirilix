<?php

namespace App\Repositories;

use App\Models\SocialMedia;

class SocialMediaRepository
{
    public function findForPublic()
    {
        return SocialMedia::where('is_published', 1)->get();
    }

    public function findAll()
    {
        return SocialMedia::with('socialMediaPlatform')->orderBy('id', 'DESC')->get();
    }

    public function findById($id)
    {
        return SocialMedia::with('socialMediaPlatform')->find($id);
    }

    public function create($data)
    {
        return SocialMedia::create($data);
    }

    public function update($data, $id)
    {
        $result = SocialMedia::find($id);
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
        $result = SocialMedia::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
