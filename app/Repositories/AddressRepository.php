<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository
{
    public function findAllUsersAddress()
    {
        return Address::where(['user_id' => auth()->user()->id])->get();
    }

    public function findAll()
    {
        return Address::get();
    }

    public function findById($id)
    {
        return Address::find($id);
    }

    public function create($data)
    {
        return Address::create($data);
    }

    public function update($data, $where)
    {
        $data = Address::where($where)->first();
        if ($data) {
            $result = Address::where($where)->update($data);
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Address::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function findByUserAndId($userId, $id)
    {
        return Address::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function getSelectedAddress($userId, $id)
    {
        return Address::with(['stateDetail'])
            ->where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }
}
