<?php

namespace App\Repositories;

use App\Models\ContactUs;

class QueryRepository
{
    public function findAll()
    {
        return ContactUs::latest()->get();
    }

    public function findForDatatable()
    {
        return ContactUs::select([
            'id',
            'name',
            'email_id',
            'phone_number',
            'message',
            'status',
            'created_at'
        ])->orderBy('id', 'desc');
    }

    public function findById($id)
    {
        return ContactUs::find($id);
    }

    public function delete($id)
    {
        $query = ContactUs::find($id);
        if ($query) {
            return $query->delete();
        }
        return false;
    }
}
