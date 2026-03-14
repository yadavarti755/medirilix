<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductMultipleImage extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'product_multiple_images';

    protected $fillable = [
        'product_id',
        'image_name',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'image_full_path',
    ];

    public function getImageFullPathAttribute()
    {
        return $this->image_name ? asset('storage/' . Config::get('file_paths')['PRODUCT_MULTIPLE_IMAGE_PATH'] . '/' . $this->image_name) : null;
    }


    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('product_multiple_image')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ProductMultipleImage model has been {$eventName}");
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
