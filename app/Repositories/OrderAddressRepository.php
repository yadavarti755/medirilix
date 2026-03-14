<?php

namespace App\Repositories;

use App\Models\OrderAddress;

class OrderAddressRepository
{
    public function findForPublic()
    {
        return OrderAddress::orderBy('id', 'desc')->get();
    }

    public function findAll($where = [])
    {
        if (!empty($where)) {
            return OrderAddress::where($where)->get();
        }
        return OrderAddress::get();
    }

    public function findOne($where = [])
    {
        if (!empty($where)) {
            return OrderAddress::where($where)->first();
        }
        return OrderAddress::first();
    }

    public function findById($id)
    {
        return OrderAddress::find($id);
    }

    public function create($data)
    {
        return OrderAddress::create($data);
    }

    public function update($data, $id)
    {
        $result = OrderAddress::find($id);
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
        $result = OrderAddress::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
