<?php

namespace App\Repositories;

use App\Models\Coupon;

class CouponRepository
{
    public function findAll()
    {
        return Coupon::latest()->get();
    }

    public function findById($id)
    {
        return Coupon::find($id);
    }

    public function findByCode($code)
    {
        return Coupon::where('code', $code)->first();
    }

    public function create($data)
    {
        $productIds = $data['product_ids'] ?? [];
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['product_ids'], $data['category_ids']); // Remove from main data

        $coupon = Coupon::create($data);

        if (!empty($productIds)) {
            $coupon->products()->sync($productIds);
        }
        if (!empty($categoryIds)) {
            $coupon->categories()->sync($categoryIds);
        }

        return $coupon;
    }

    public function update($data, $id)
    {
        $result = Coupon::find($id);
        if ($result) {
            $productIds = $data['product_ids'] ?? [];
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['product_ids'], $data['category_ids']);

            $result->update($data);

            // Sync relations if present (even if empty, to clear them if user unchecked everything)
            // But checking isset/key existence in data passed from Service
            if (isset($productIds)) { // Assumption: Service passes empty array if cleared
                $result->products()->sync($productIds);
            }
            if (isset($categoryIds)) {
                $result->categories()->sync($categoryIds);
            }

            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Coupon::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
