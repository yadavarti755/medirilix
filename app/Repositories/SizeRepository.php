<?php

namespace App\Repositories;

use App\Models\Size;

class SizeRepository
{
    public function findForPublic()
    {
        return Size::get();
    }

    public function findAll()
    {
        return Size::get();
    }

    public function findById($id)
    {
        return Size::find($id);
    }

    public function create($data)
    {
        return Size::create($data);
    }

    public function update($data, $id)
    {
        $result = Size::find($id);
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
        $result = Size::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
