<?php

namespace App\Services;

use App\DTO\ProductDto;
use App\DTO\ProductFilterDto;
use App\Repositories\ProductRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class ProductService
{
    use FileUploadTraits;
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function findForDatatable($where = [])
    {
        return $this->repository->findForDatatable($where);
    }

    public function findAll($where = [], $limit = 10)
    {
        return $this->repository->findAll($where, $limit);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findMostViewed($limit = 10)
    {
        return $this->repository->findMostViewed($limit);
    }

    public function create(ProductDto $dto)
    {

        if ($dto->featured_image) {
            $file = $this->uploadFile($dto->featured_image, Config::get('file_paths')['PRODUCT_IMAGE_PATH']);
            $dto->featured_image = $file['file_name'];
        }

        $result = $this->repository->create([
            'category_id' => $dto->category_id,
            'name' => $dto->name,
            'mrp' => $dto->mrp,
            'selling_price' => $dto->selling_price,
            'upc' => $dto->upc,
            'brand_id' => $dto->brand_id,
            'type_id' => $dto->type_id,
            'intended_use_id' => $dto->intended_use_id,
            'model' => $dto->model,
            'mpn' => $dto->mpn,
            'expiration_date' => $dto->expiration_date,
            'california_prop_65_warning' => $dto->california_prop_65_warning,
            'country_of_origin' => $dto->country_of_origin,
            'unit_quantity' => $dto->unit_quantity,
            'unit_type_id' => $dto->unit_type_id,
            'return_till_days' => $dto->return_till_days,
            'return_description' => $dto->return_description,
            'return_policy_id' => $dto->return_policy_id,
            'description' => $dto->description,
            'featured_image' => $dto->featured_image,
            'meta_keywords' => $dto->meta_keywords,
            'meta_description' => $dto->meta_description,
            'material_id' => $dto->material_id,
            'product_listing_type' => $dto->product_listing_type,
            'quantity' => $dto->quantity,
            'available_quantity' => $dto->available_quantity,
            'stock_availability' => $dto->stock_availability,
            'is_published' => $dto->is_published,
            'created_by' => $dto->created_by,
            'updated_by' => $dto->updated_by,
        ]);

        if (!$result) {
            return false;
        }

        // if (!empty($dto->sizes)) {
        //    $result->sizes()->sync($dto->sizes);
        // }

        return $result;
    }


    public function update(ProductDto $dto, $id)
    {
        if ($dto->featured_image) {
            $file = $this->uploadFile($dto->featured_image, Config::get('file_paths')['PRODUCT_IMAGE_PATH']);
            $dto->featured_image = $file['file_name'];
        }

        $updateData = [
            'category_id' => $dto->category_id,
            'name' => $dto->name,
            'mrp' => $dto->mrp,
            'selling_price' => $dto->selling_price,
            'upc' => $dto->upc,
            'brand_id' => $dto->brand_id,
            'type_id' => $dto->type_id,
            'intended_use_id' => $dto->intended_use_id,
            'model' => $dto->model,
            'mpn' => $dto->mpn,
            'expiration_date' => $dto->expiration_date,
            'california_prop_65_warning' => $dto->california_prop_65_warning,
            'country_of_origin' => $dto->country_of_origin,
            'unit_quantity' => $dto->unit_quantity,
            'unit_type_id' => $dto->unit_type_id,
            'return_till_days' => $dto->return_till_days,
            'return_description' => $dto->return_description,
            'return_policy_id' => $dto->return_policy_id,
            'description' => $dto->description,
            'meta_keywords' => $dto->meta_keywords,
            'meta_description' => $dto->meta_description,
            'material_id' => $dto->material_id,
            'product_listing_type' => $dto->product_listing_type,
            'quantity' => $dto->quantity,
            'available_quantity' => $dto->available_quantity,
            'stock_availability' => $dto->stock_availability,
            'is_published' => $dto->is_published,
            'updated_by' => $dto->updated_by,
        ];
        if ($dto->featured_image) {
            $updateData['featured_image'] = $dto->featured_image;
        }

        $result = $this->repository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function getMinMaxPrice()
    {
        return $this->repository->getMinMaxPrice();
    }

    public function resolveSorting($filter)
    {
        $orderBy = 'id';
        $orderType = 'DESC';

        if ($filter == 'SORT_POPULARITY') {
            $orderBy = 'view_count';
        } elseif ($filter == 'SORT_LATEST') {
            $orderBy = 'id';
        } elseif ($filter == 'PRICE_LOW_TO_HIGH') {
            $orderBy = 'selling_price';
            $orderType = 'ASC';
        } elseif ($filter == 'PRICE_HIGH_TO_LOW') {
            $orderBy = 'selling_price';
            $orderType = 'DESC';
        }

        return [$orderBy, $orderType];
    }

    public function filterByCategory($categoryId, $where, $orderBy, $orderType, $limit)
    {
        return $this->repository->filterByCategory($categoryId, $where, $orderBy, $orderType, $limit);
    }

    public function filterDiscounted($maxSellingPrice, $where, $orderBy, $orderType, $limit)
    {
        return $this->repository->filterDiscounted($maxSellingPrice, $where, $orderBy, $orderType, $limit);
    }

    public function filterAll($where, $orderBy, $orderType, $limit)
    {
        return $this->repository->filterAll($where, $orderBy, $orderType, $limit);
    }

    public function searchProducts($keyword, $where, $orderBy, $orderType, $limit)
    {
        return $this->repository->searchProducts($keyword, $where, $orderBy, $orderType, $limit);
    }

    public function searchNames($keyword, $limit = 4)
    {
        return $this->repository->searchNames($keyword, $limit);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function incrementViewCount($slug)
    {
        return $this->repository->incrementViewCount($slug);
    }

    public function getRelatedProducts($categoryId, $slug, $limit = 8)
    {
        return $this->repository->getRelatedProducts($categoryId, $slug, $limit);
    }

    public function getAlsoLikeProducts($excludeSlug)
    {
        return $this->repository->getAlsoLikeProducts($excludeSlug);
    }

    public function findByProduct($productId)
    {
        return $this->repository->findByProduct($productId);
    }

    public function filterProducts(ProductFilterDto $dto)
    {
        return $this->repository->filterByDto($dto);
    }

    public function publish(ProductDto $dto, $id)
    {
        return $this->update($dto, $id);
    }
    public function decrementStock($productId, $quantity)
    {
        return $this->repository->decrementStock($productId, $quantity);
    }
}
