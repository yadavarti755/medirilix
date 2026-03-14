<?php

namespace App\Repositories;

use App\Models\Media;

class MediaRepository
{
    public function findAllWithPagination($perPage = 12, $where = [], $search = null)
    {
        $query = Media::query();

        // Apply where conditions if provided
        if (!empty($where)) {
            $query->where($where);
        }

        // Apply search if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('original_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('alt_text', 'LIKE', '%' . $search . '%')
                    ->orWhere('alt_text_hi', 'LIKE', '%' . $search . '%')
                    ->orWhere('mime_type', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findAll()
    {
        return Media::orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return Media::find($id);
    }

    public function create($data)
    {
        return Media::create($data);
    }

    public function update($data, $id)
    {
        $result = Media::find($id);
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
        $result = Media::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
