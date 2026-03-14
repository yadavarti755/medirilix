<?php

namespace App\Repositories;

use App\Models\OrderProductList;

class OrderProductListRepository
{
    public function findForPublic()
    {
        return OrderProductList::orderBy('id', 'desc')->get();
    }

    public function findAll()
    {
        return OrderProductList::get();
    }

    public function findById($id)
    {
        return OrderProductList::find($id);
    }

    public function create($data)
    {
        return OrderProductList::create($data);
    }

    public function update($data, $id)
    {
        $result = OrderProductList::find($id);
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
        $result = OrderProductList::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function updateByOrderNumber($orderNumber, $data)
    {
        return OrderProductList::where('order_number', $orderNumber)->update($data);
    }

    public function updateByOrderNumberAndUser($orderNumber, $userId, $data)
    {
        return OrderProductList::where([
            'order_number' => $orderNumber,
            'user_id' => $userId
        ])->update($data);
    }

    public function updateByProductAndOrderAndUser($productId, $orderNumber, $userId, $data)
    {
        return OrderProductList::where([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_number' => $orderNumber,
        ])->update($data);
    }

    public function findByOrderNumber($orderNumber)
    {
        return OrderProductList::where('order_number', $orderNumber)->get();
    }

    public function findByOrderNumberAndUser($orderNumber, $userId)
    {
        return OrderProductList::where([
            'order_number' => $orderNumber,
            'user_id' => $userId
        ])->get();
    }
}
