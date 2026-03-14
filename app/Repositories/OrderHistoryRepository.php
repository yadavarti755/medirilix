<?php

namespace App\Repositories;

use App\Models\OrderHistory;

class OrderHistoryRepository
{
    public function findForPublic()
    {
        return OrderHistory::orderBy('id', 'desc')->get();
    }

    public function findAll()
    {
        return OrderHistory::get();
    }

    public function findById($id)
    {
        return OrderHistory::find($id);
    }

    public function create($data)
    {
        return OrderHistory::create($data);
    }

    public function update($data, $id)
    {
        $result = OrderHistory::find($id);
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
        $result = OrderHistory::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
