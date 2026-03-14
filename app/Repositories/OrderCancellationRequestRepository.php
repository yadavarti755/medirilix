<?php

namespace App\Repositories;

use App\Models\OrderCancellationRequest;

class OrderCancellationRequestRepository
{
    public function create(array $data)
    {
        return OrderCancellationRequest::create($data);
    }

    public function update(array $data, $id)
    {
        $request = OrderCancellationRequest::find($id);
        if ($request) {
            $request->update($data);
            return $request;
        }
        return null;
    }

    public function findById($id)
    {
        return OrderCancellationRequest::with(['messages.sender', 'user', 'orderProductList.product'])->find($id);
    }

    public function findByOrderProductListId($id)
    {
        return OrderCancellationRequest::with(['messages.sender'])->where('order_product_list_id', $id)->first();
    }

    public function findAll()
    {
        return OrderCancellationRequest::with(['user', 'orderProductList.product', 'orderProductList.order', 'cancelReason'])->latest()->get();
    }
}
