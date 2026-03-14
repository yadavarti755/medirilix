<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findForDatatable($where = [])
    {
        if ($where) {
            return Product::where($where)->orderBy('id', 'DESC');
        }
        return Product::orderBy('id', 'DESC');
    }
    public function findAll($where = [], $limit = 10)
    {
        if ($where) {
            return Product::where($where)->orderBy('id', 'DESC')->limit($limit)->get();
        }
        return Product::orderBy('id', 'DESC')->limit($limit)->get();
    }

    public function findById($id)
    {
        return Product::find($id);
    }

    public function checkStock($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        return [
            'available_quantity' => $product->available_quantity,
            'stock_status'       => $product->stock_availability,
        ];
    }

    public function getPrice($product)
    {
        return $product->selling_price ?: $product->mrp;
    }


    public function findMostViewed($limit = 10)
    {
        return Product::limit($limit)->get();
    }

    public function create($data)
    {
        return Product::create($data);
    }

    public function update($data, $id)
    {
        $result = Product::find($id);
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
        $result = Product::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function getMinMaxPrice()
    {
        return Product::selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')->first();
    }

    public function filterByCategory($categoryId, $where, $orderBy, $orderType, $limit)
    {
        return Product::where('category_id', $categoryId)
            ->where($where)
            ->orderBy($orderBy, $orderType)
            ->paginate($limit);
    }

    public function filterDiscounted($maxSellingPrice, $where, $orderBy, $orderType, $limit)
    {
        return Product::where('selling_price', '<=', $maxSellingPrice)
            ->where($where)
            ->orderBy($orderBy, $orderType)
            ->paginate($limit);
    }

    public function filterAll($where, $orderBy, $orderType, $limit)
    {
        return Product::where($where)
            ->orderBy($orderBy, $orderType)
            ->paginate($limit);
    }

    public function searchProducts($keyword, $where, $orderBy, $orderType, $limit)
    {
        return Product::where('name', 'LIKE', "%$keyword%")
            ->orWhere('slug', 'LIKE', "%$keyword%")
            ->where($where)
            ->orderBy($orderBy, $orderType)
            ->paginate($limit);
    }

    public function searchNames($keyword, $limit = 4)
    {
        return Product::where('name', 'LIKE', "%$keyword%")
            ->limit($limit)
            ->get();
    }

    public function findBySlug($slug)
    {
        return Product::where('slug', $slug)->first();
    }

    public function incrementViewCount($slug)
    {
        return Product::where('slug', $slug)->increment('view_count');
    }

    public function getRelatedProducts($categoryId, $slug, $limit = 8)
    {
        return Product::where('category_id', $categoryId)
            ->where('slug', '!=', $slug)
            ->limit($limit)
            ->get();
    }

    public function getAlsoLikeProducts($excludeSlug)
    {
        return Product::where('slug', '!=', $excludeSlug)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    public function findByProduct($productId)
    {
        return Product::where('id', $productId)->first();
    }

    public function filterByDto(\App\DTO\ProductFilterDto $dto)
    {
        $query = Product::query();

        $query->where('is_published', 1);

        // Search
        if ($dto->search) {
            $query->where(function ($q) use ($dto) {
                $q->where('name', 'LIKE', '%' . $dto->search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $dto->search . '%');
            });
        }

        // Category Filter (Slug)
        if (!empty($dto->category_slugs)) {
            $categoryIds = \App\Models\Category::whereIn('slug', $dto->category_slugs)->pluck('id')->toArray();
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Brand Filter
        if (!empty($dto->brand_ids)) {
            $query->whereIn('brand_id', $dto->brand_ids);
        }

        // Price Filter
        if ($dto->min_price !== null && $dto->max_price !== null) {
            $query->whereBetween('selling_price', [$dto->min_price, $dto->max_price]);
        }

        // Type Filter
        if (!empty($dto->types)) {
            $typeIds = [];
            $filterTypes = \Illuminate\Support\Facades\Config::get('constants.filter_by_type_code');
            foreach ($dto->types as $type) {
                if (isset($filterTypes[$type])) {
                    $typeIds[] = $filterTypes[$type];
                }
            }

            if (!empty($typeIds)) {
                $query->whereIn('product_listing_type', $typeIds);
            }
        }

        // Stock Availability (Default)
        $query->where('stock_availability', 1);


        // Sorting
        $orderBy = 'id';
        $orderType = 'DESC';

        if ($dto->sort) {
            if ($dto->sort == 'SORT_POPULARITY') {
                $orderBy = 'view_count';
            } elseif ($dto->sort == 'SORT_LATEST') {
                $orderBy = 'id';
            } elseif ($dto->sort == 'PRICE_LOW_TO_HIGH') {
                $orderBy = 'selling_price';
                $orderType = 'ASC';
            } elseif ($dto->sort == 'PRICE_HIGH_TO_LOW') {
                $orderBy = 'selling_price';
                $orderType = 'DESC';
            }
        }

        $query->orderBy($orderBy, $orderType);

        return $query->paginate($dto->per_page);
    }
    public function decrementStock($productId, $quantity)
    {
        $product = Product::find($productId);
        if ($product) {
            if ($product->available_quantity >= $quantity) {
                $product->decrement('available_quantity', $quantity);
                if ($product->fresh()->available_quantity <= 0) {
                    $product->update(['stock_availability' => 0]);
                }
                return true;
            }
        }
        return false;
    }
}
