<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function findForPublic($where = [])
    {
        if ($where) {
            return Offer::where($where)->get();
        }
        return Offer::get();
    }

    public function findAll()
    {
        return Offer::get();
    }

    public function findById($id)
    {
        return Offer::find($id);
    }

    public function create($data)
    {
        return Offer::create($data);
    }

    public function update($data, $id)
    {
        $result = Offer::find($id);
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
        $result = Offer::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
