<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class AuthenticationLogRepository
{
    public function findAll()
    {
        return Auth::user()->authentications()->latest()->paginate(10)->get();
    }

    public function findAllForDatatable()
    {
        return Auth::user()->authentications()->latest()->paginate(10);
    }
}
