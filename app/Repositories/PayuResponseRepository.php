<?php

namespace App\Repositories;

use App\Models\PayuResponse;

class PayuResponseRepository
{
    public function create(array $data)
    {
        return PayuResponse::create($data);
    }
}
