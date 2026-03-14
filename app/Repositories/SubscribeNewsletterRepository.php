<?php

namespace App\Repositories;

use App\Models\SubscribeNewsletter;

class SubscribeNewsletterRepository
{
    public function findAll()
    {
        return SubscribeNewsletter::latest()->get();
    }

    public function findById($id)
    {
        return SubscribeNewsletter::find($id);
    }

    public function create($data)
    {
        return SubscribeNewsletter::create($data);
    }

    public function delete($id)
    {
        $result = SubscribeNewsletter::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
