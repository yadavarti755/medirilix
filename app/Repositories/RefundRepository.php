<?php

namespace App\Repositories;

use App\Models\Refund;

class RefundRepository
{
    public function create(array $data)
    {
        return Refund::create($data);
    }

    public function update($id, array $data)
    {
        $refund = Refund::find($id);
        if ($refund) {
            $refund->update($data);
            return $refund;
        }
        return null;
    }

    public function findById($id)
    {
        return Refund::find($id);
    }

    public function findByOrderProductListId($id)
    {
        return Refund::where('order_product_list_id', $id)->first();
    }

    public function getAll()
    {
        return Refund::with('orderProductList.product')->latest()->get();
    }
}
