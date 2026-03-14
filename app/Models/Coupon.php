<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Coupon extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'value',
        'min_spend',
        'max_discount',
        'usage_limit_per_coupon',
        'usage_limit_per_user',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'min_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('coupon')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Coupon has been {$eventName}");
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }
}
