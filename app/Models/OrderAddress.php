<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OrderAddress extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'order_addresses';

    protected $fillable = [
        'user_id',
        'order_number',
        'person_name',
        'person_contact_number',
        'person_alt_contact_number',
        'address',
        'locality',
        'landmark',
        'city',
        'state',
        'country',
        'pincode',
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
            ->useLogName('order_address')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Order Address model has been {$eventName}");
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
