<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Wishlist extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Configure Spatie activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('wishlist')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Wishlist model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
