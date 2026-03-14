<?php

namespace App\Repositories;

use App\Models\SearchTag;

class SearchTagRepository
{
    public function findAll()
    {
        return SearchTag::orderBy('id', 'ASC')->get();
    }

    public function findById($id)
    {
        return SearchTag::find($id);
    }

    public function create($data)
    {
        return SearchTag::create($data);
    }

    public function update($data, $id)
    {
        $result = SearchTag::find($id);
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
        $result = SearchTag::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
