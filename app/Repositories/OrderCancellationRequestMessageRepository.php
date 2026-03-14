<?php

namespace App\Repositories;

use App\Models\OrderCancellationRequestMessage;

class OrderCancellationRequestMessageRepository
{
    public function create(array $data)
    {
        return OrderCancellationRequestMessage::create($data);
    }
}
