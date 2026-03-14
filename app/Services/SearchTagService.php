<?php

namespace App\Services;

use App\DTO\ProductDto;
use App\Repositories\SearchTagRepository;

class SearchTagService
{
    private $searchTagRepository;

    public function __construct()
    {
        $this->searchTagRepository = new SearchTagRepository();
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->searchTagRepository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->searchTagRepository->findById($id);
    }

    public function create(ProductDto $productDto)
    {

        $result = $this->searchTagRepository->create([
            'category_id' => $productDto->category_id,
            'name' => $productDto->name,
            'mrp' => $productDto->mrp,
            'selling_price' => $productDto->selling_price,
            'description' => $productDto->description,
            'featured_image' => $productDto->featured_image,
            'meta_keywords' => $productDto->meta_keywords,
            'meta_description' => $productDto->meta_description,
            'material_id' => $productDto->material_id,
            'product_listing_type' => $productDto->product_listing_type,
            'quantity' => $productDto->quantity,
            'available_quantity' => $productDto->available_quantity,
            'stock_availability' => $productDto->stock_availability,
            'is_published' => $productDto->is_published,
            'created_by' => $productDto->created_by,
            'updated_by' => $productDto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function update(ProductDto $productDto, $id)
    {
        $updateData = [
            'category_id' => $productDto->category_id,
            'name' => $productDto->name,
            'mrp' => $productDto->mrp,
            'selling_price' => $productDto->selling_price,
            'description' => $productDto->description,
            'featured_image' => $productDto->featured_image,
            'meta_keywords' => $productDto->meta_keywords,
            'meta_description' => $productDto->meta_description,
            'material_id' => $productDto->material_id,
            'product_listing_type' => $productDto->product_listing_type,
            'quantity' => $productDto->quantity,
            'available_quantity' => $productDto->available_quantity,
            'stock_availability' => $productDto->stock_availability,
            'is_published' => $productDto->is_published,
            'updated_by' => $productDto->updated_by,
        ];

        $result = $this->searchTagRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->searchTagRepository->delete($id);
    }

    public function searchTags($query, $limit = 10)
    {
        return $this->searchTagRepository->searchTags($query, $limit);
    }
}
