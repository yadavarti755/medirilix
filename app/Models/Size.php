<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Size extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'sizes';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('size')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Size model has been {$eventName}");
    }

    /**
     * Relationships
     */
    // public function productSizes()
    // {
    //     return $this->hasMany(ProductSize::class, 'size_id');
    // }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'product_sizes', 'size_id', 'product_id')
    //         ->withTimestamps();
    // }
}
