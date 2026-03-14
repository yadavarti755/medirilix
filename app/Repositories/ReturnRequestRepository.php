<?php

namespace App\Repositories;

use App\Models\ReturnRequest;

class ReturnRequestRepository
{
    public function create(array $data)
    {
        return ReturnRequest::create($data);
    }

    public function update(array $data, $id)
    {
        $request = ReturnRequest::find($id);
        if ($request) {
            $request->update($data);
            return $request;
        }
        return false;
    }

    public function find($id)
    {
        return ReturnRequest::with(['user', 'orderProductList.product', 'returnReason'])->find($id);
    }

    public function delete($id)
    {
        $request = ReturnRequest::find($id);
        if ($request) {
            return $request->delete();
        }
        return false;
    }

    public function findAll()
    {
        return ReturnRequest::with(['user', 'orderProductList.product', 'returnReason'])->orderBy('id', 'desc')->get();
    }
    public function findByOrderNumber($userId, $orderNumber, $status)
    {
        return ReturnRequest::where('user_id', $userId)
            ->where('order_number', $orderNumber)
            ->where('return_status', $status)
            ->first();
    }

    public function checkExists($userId, $orderNumber, $productId, $excludedStatuses = [])
    {
        $query = ReturnRequest::where('user_id', $userId)
            ->where('order_number', $orderNumber)
            ->where('product_id', $productId);

        if (!empty($excludedStatuses)) {
            $query->whereNotIn('return_status', $excludedStatuses);
        }

        return $query->first();
    }

    public function findByOrderProductListId($orderProductListId)
    {
        return ReturnRequest::where('order_product_list_id', $orderProductListId)->latest()->first();
    }

    public function updateStatus($orderNumber, $productId, $userId, $currentStatus, $newStatus, $updatedBy)
    {
        return ReturnRequest::where('order_number', $orderNumber)
            ->where('product_id', $productId)
            ->where('user_id', $userId)
            // ->where('return_status', $currentStatus) // Strict check optional?
            ->update([
                'return_status' => $newStatus,
                'updated_by' => $updatedBy
            ]);
    }

    public function findByReturnCode($returnCode)
    {
        return ReturnRequest::where('return_code', $returnCode)->get();
    }

    public function findAllAdmin($filters = [])
    {
        // Basic implementation, add filters if needed
        return ReturnRequest::with(['user', 'orderProductList.product', 'returnReason'])->orderBy('id', 'desc')->get();
    }
}
