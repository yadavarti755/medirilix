<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OrderHistory extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'order_histories';

    protected $fillable = [
        'user_id',
        'order_number',
        'order_status',
        'status_changed_date',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'status_desc',
    ];

    /**
     * Accessor for human-readable status
     */
    public function getStatusDescAttribute()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('order_history')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Order History model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
