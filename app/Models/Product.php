<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use SoftDeletes, LogsActivity, HasSlug;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'mrp',
        'selling_price',
        'upc',
        'brand_id',
        'type_id',
        'intended_use_id',
        'model',
        'mpn',
        'expiration_date',
        'california_prop_65_warning',
        'country_of_origin',
        'unit_quantity',
        'unit_type_id',
        'description',
        'featured_image',
        'meta_keywords',
        'meta_description',
        'material_id',
        'product_listing_type',
        'quantity',
        'available_quantity',
        'stock_availability',
        'view_count',
        'is_published',
        'return_till_days',
        'return_description',
        'return_policy_id',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'is_published_desc',
        'stock_availability_desc',
        'featured_image_full_path',
    ];

    public function getFeaturedImageFullPathAttribute()
    {
        return $this->featured_image ? asset('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/' . $this->featured_image) : null;
    }

    /**
     * Accessor for readable status text
     */
    public function getIsPublishedDescAttribute()
    {
        return $this->is_published == 1 ? 'Published' : 'Not Published';
    }

    /**
     * Accessor for readable stock availability text
     */
    public function getStockAvailabilityDescAttribute()
    {
        return $this->stock_availability == 1 ? 'In Stock' : 'Out of Stock';
    }

    /**
     * Function to generate slug
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')  // Field to generate slug from
            ->saveSlugsTo('slug')         // Field to save slug into
            ->doNotGenerateSlugsOnUpdate(); // Optional: keeps original slug when updating title
    }

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('product')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Product model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }

    // public function productSizes()
    // {
    //     return $this->hasMany(ProductSize::class, 'product_id', 'id');
    // }

    // public function sizes()
    // {
    //     return $this->belongsToMany(Size::class, 'product_sizes', 'product_id', 'size_id')
    //         ->withTimestamps();
    // }

    public function images()
    {
        return $this->hasMany(ProductMultipleImage::class, 'product_id', 'id');
    }

    public function otherSpecifications()
    {
        return $this->hasMany(ProductOtherSpecification::class, 'product_id', 'id');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'type_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function intendedUse()
    {
        return $this->belongsTo(IntendedUse::class, 'intended_use_id', 'id');
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id', 'id');
    }

    public function returnPolicy()
    {
        return $this->belongsTo(ReturnPolicy::class, 'return_policy_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_of_origin', 'id');
    }
}
