<?php

namespace App\Repositories;

use App\Models\CustomerReview;
use App\Models\CustomerReviewImage;

class CustomerReviewRepository
{
    public function findAll($limit = null)
    {
        $query = CustomerReview::with(['user', 'product', 'images'])->orderBy('id', 'DESC');
        if ($limit) {
            return $query->paginate($limit);
        }
        return $query->get();
    }

    public function findById($id)
    {
        return CustomerReview::with(['user', 'product', 'images'])->find($id);
    }

    public function findByProductId($productId)
    {
        return CustomerReview::where('product_id', $productId)
            ->where('is_active', true)
            ->with(['user', 'images'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function create(array $data)
    {
        return CustomerReview::create($data);
    }

    public function createImage(array $data)
    {
        return CustomerReviewImage::create($data);
    }

    public function update(array $data, $id)
    {
        $review = CustomerReview::find($id);
        if ($review) {
            $review->update($data);
            return $review;
        }
        return false;
    }

    public function delete($id)
    {
        $review = CustomerReview::find($id);
        if ($review) {
            // Delete images first (soft delete)
            $review->images()->delete();
            return $review->delete();
        }
        return false;
    }
}
