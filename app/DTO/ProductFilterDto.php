<?php

namespace App\DTO;

class ProductFilterDto
{
    public $search;
    public $category_slugs;
    public $brand_ids;
    public $min_price;
    public $max_price;
    public $types; // Filter type: latest, popular, etc.
    public $sort;
    public $per_page;

    public function __construct(
        $search = null,
        $category_slugs = [],
        $brand_ids = [],
        $min_price = null,
        $max_price = null,
        $types = [],
        $sort = null,
        $per_page = 18
    ) {
        $this->search = $search;
        $this->category_slugs = $category_slugs;
        $this->brand_ids = $brand_ids;
        $this->min_price = $min_price;
        $this->max_price = $max_price;
        $this->types = $types;
        $this->sort = $sort;
        $this->per_page = $per_page;
    }
}
