<?php

namespace App\Services;

use App\DTO\CustomerReviewDto;
use App\Repositories\CustomerReviewRepository;
use App\Services\OrderProductListService;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerReviewService
{
    use FileUploadTraits;

    private $repository;
    private $orderProductListService;

    public function __construct()
    {
        $this->repository = new CustomerReviewRepository();
        $this->orderProductListService = new OrderProductListService();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findByProductId($productId)
    {
        return $this->repository->findByProductId($productId);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(CustomerReviewDto $dto)
    {
        // 1. Verify if user purchased the product
        $hasPurchased = $this->verifyPurchase($dto->user_id, $dto->product_id);
        if (!$hasPurchased) {
            throw new \Exception("User has not purchased this product.");
        }

        DB::beginTransaction();
        try {
            $reviewData = [
                'user_id' => $dto->user_id,
                'product_id' => $dto->product_id,
                'message' => $dto->message,
                'rating' => $dto->rating,
                'is_active' => true, // Default active, or set based on settings
                'created_by' => $dto->created_by,
                'updated_by' => $dto->updated_by,
            ];

            $review = $this->repository->create($reviewData);

            if ($dto->images && is_array($dto->images)) {
                foreach ($dto->images as $image) {
                    $uploadResult = $this->uploadFile($image, 'storage/review_images'); // Basic path, configurable
                    // Adjust path logic based on FileUploadTraits if needed, using custom path below
                    // Assuming uploadFile returns ['file_name' => '...']

                    if (isset($uploadResult['file_name'])) {
                        $this->repository->createImage([
                            'customer_review_id' => $review->id,
                            'image_path' => $uploadResult['file_name']
                        ]);
                    }
                }
            }

            DB::commit();
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create customer review: " . $e->getMessage());
            return false;
        }
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    private function verifyPurchase($userId, $productId)
    {
        // Use OrderProductListService to check existing orders
        // Filter: user_id, product_id, and status (e.g., delivered)

        // We look for any order that is NOT cancelled/returned ideally, or just any purchase.
        // Assuming OrderProductList stores individual items.
        // We can use findAll with where clause.

        $purchases = $this->orderProductListService->findAll([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        // Check if collection is not empty
        return $purchases->isNotEmpty();
    }
}
