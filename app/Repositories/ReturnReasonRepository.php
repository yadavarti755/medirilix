<?php

namespace App\Repositories;

use App\Models\ReturnReason;

class ReturnReasonRepository
{
    public function create(array $data)
    {
        return ReturnReason::create($data);
    }

    public function update(array $data, $id)
    {
        $reason = ReturnReason::find($id);
        if ($reason) {
            $reason->update($data);
            return $reason;
        }
        return false;
    }

    public function find($id)
    {
        return ReturnReason::find($id);
    }

    public function delete($id)
    {
        $reason = ReturnReason::find($id);
        if ($reason) {
            return $reason->delete();
        }
        return false;
    }

    public function findAll()
    {
        return ReturnReason::orderBy('id', 'desc')->get();
    }
}
