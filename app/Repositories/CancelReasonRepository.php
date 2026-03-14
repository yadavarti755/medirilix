<?php

namespace App\Repositories;

use App\Models\CancelReason;

class CancelReasonRepository
{
    public function create(array $data)
    {
        return CancelReason::create($data);
    }

    public function update(array $data, $id)
    {
        $reason = CancelReason::find($id);
        if ($reason) {
            $reason->update($data);
            return $reason;
        }
        return false;
    }

    public function find($id)
    {
        return CancelReason::find($id);
    }

    public function delete($id)
    {
        $reason = CancelReason::find($id);
        if ($reason) {
            return $reason->delete();
        }
        return false;
    }

    public function findAll()
    {
        return CancelReason::orderBy('id', 'desc')->get();
    }
}
