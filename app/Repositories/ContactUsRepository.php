<?php

namespace App\Repositories;

use App\Models\ContactUs;

class ContactUsRepository
{
    public function create($data)
    {
        return ContactUs::create($data);
    }
}
