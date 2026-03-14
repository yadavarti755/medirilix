<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductSize extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'product_sizes';

    protected $fillable = [
        'product_id',
        'size_id',
    ];

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('product_size')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ProductSize model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }
}
