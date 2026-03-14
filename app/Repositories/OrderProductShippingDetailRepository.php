<?php

namespace App\Repositories;

use App\Models\OrderProductShippingDetail;

class OrderProductShippingDetailRepository
{
    public function create(array $data)
    {
        return OrderProductShippingDetail::create($data);
    }

    public function findByOrderProductListId($orderProductListId)
    {
        return OrderProductShippingDetail::where('order_product_list_id', $orderProductListId)->first();
    }

    public function update($data, $id)
    {
        $result = OrderProductShippingDetail::find($id);
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
        $result = OrderProductShippingDetail::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
