<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentMethodRepository
{
    public function findForPublic()
    {
        return PaymentMethod::where('is_published', 1)->get();
    }

    public function findAll()
    {
        return PaymentMethod::all();
    }

    public function findById($id)
    {
        return PaymentMethod::find($id);
    }

    public function create($data)
    {
        return PaymentMethod::create($data);
    }

    public function update($data, $id)
    {
        $result = PaymentMethod::find($id);
        if ($result) {
            $updateStatus = $result->update($data);
            if (!$updateStatus) {
                return false;
            }
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = PaymentMethod::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
