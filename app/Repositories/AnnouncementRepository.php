<?php

namespace App\Repositories;

use App\Models\Announcement;

class AnnouncementRepository
{
    public function findForPublic($limit = 10)
    {
        return Announcement::where('is_published', 1)->limit($limit)->orderBy('published_date', 'desc')->get();
    }

    public function findForPublicHomepage($limit = 10)
    {
        return Announcement::where([
            'status' => 1,
            'is_published' => 1
        ])->limit($limit)->orderBy('id', 'desc')->get();
    }

    public function findAll()
    {
        return Announcement::orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return Announcement::find($id);
    }

    public function create($data)
    {
        return Announcement::create($data);
    }

    public function update($data, $id)
    {
        $result = Announcement::find($id);
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
        $result = Announcement::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function search(array $fields, string $term)
    {
        return Announcement::where(function ($query) use ($fields, $term) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }
        })
            ->orderBy('id', 'desc')
            ->get();
    }
}
