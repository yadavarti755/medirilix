<?php

namespace App\DTO;

class ProductDto
{
    public $category_id;
    public $name;
    public $mrp;
    public $selling_price;

    // Newly added fields
    public $upc;
    public $brand_id;
    public $type_id;
    public $intended_use_id;
    public $model;
    public $mpn;
    public $expiration_date;
    public $california_prop_65_warning;
    public $country_of_origin;
    public $unit_quantity;
    public $unit_type_id;
    public $return_till_days;
    public $return_description;
    public $return_policy_id;

    public $description;
    public $featured_image;
    public $meta_keywords;
    public $meta_description;
    public $material_id;
    public $product_listing_type;
    public $quantity;
    public $available_quantity;
    public $stock_availability;
    public $is_published;
    public $created_by;
    public $updated_by;

    public function __construct(
        $category_id = null,
        $name,
        $mrp,
        $selling_price,
        $upc = null,
        $brand_id = null,
        $type_id = null,
        $intended_use_id = null,
        $model = null,
        $mpn = null,
        $expiration_date = null,
        $california_prop_65_warning = null,
        $country_of_origin = null,
        $unit_quantity = null,
        $unit_type_id = null,
        $return_till_days = null,
        $return_description = null,
        $return_policy_id = null,
        $description = null,
        $featured_image = 'no-image.png',
        $meta_keywords = null,
        $meta_description = null,
        $material_id = null,
        $product_listing_type = 0,
        $quantity = null,
        $available_quantity = null,
        $stock_availability = 1,
        $is_published = 1,
        $created_by = null,
        $updated_by = null
    ) {
        $this->category_id = $category_id;
        $this->name = $name;
        $this->mrp = $mrp;
        $this->selling_price = $selling_price;

        // Newly added fields
        $this->upc = $upc;
        $this->brand_id = $brand_id;
        $this->type_id = $type_id;
        $this->intended_use_id = $intended_use_id;
        $this->model = $model;
        $this->mpn = $mpn;
        $this->expiration_date = $expiration_date;
        $this->california_prop_65_warning = $california_prop_65_warning;
        $this->country_of_origin = $country_of_origin;
        $this->unit_quantity = $unit_quantity;
        $this->unit_type_id = $unit_type_id;
        $this->return_till_days = $return_till_days;
        $this->return_description = $return_description;
        $this->return_policy_id = $return_policy_id;

        $this->description = $description;
        $this->featured_image = $featured_image;
        $this->meta_keywords = $meta_keywords;
        $this->meta_description = $meta_description;
        $this->material_id = $material_id;
        $this->product_listing_type = $product_listing_type;
        $this->quantity = $quantity;
        $this->available_quantity = $available_quantity;
        $this->stock_availability = $stock_availability;
        $this->is_published = $is_published;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
