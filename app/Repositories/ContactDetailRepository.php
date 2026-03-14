<?php

namespace App\Repositories;

use App\Models\ContactDetail;

class ContactDetailRepository
{
    public function findAll()
    {
        return ContactDetail::orderBy('id', 'DESC')->get();
    }

    public function findById($id)
    {
        return ContactDetail::find($id);
    }

    public function create($data)
    {
        return ContactDetail::create($data);
    }

    public function update($data, $id)
    {
        $result = ContactDetail::find($id);
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
        $result = ContactDetail::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
