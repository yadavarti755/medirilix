<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public function create(array $data)
    {
        return Transaction::create($data);
    }

    public function updateStatus($orderNumber, $txnId, $userId, array $data)
    {
        return Transaction::where([
            'order_number' => $orderNumber,
            'txn_id' => $txnId,
            'user_id' => $userId
        ])->update($data);
    }
}
