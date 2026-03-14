<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Address extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'type',
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
        'type_desc',
        'status_desc',
    ];

    /**
     * Accessor for human-readable type
     */
    public function getTypeDescAttribute()
    {
        return match ((int) $this->type) {
            1 => 'Home',
            2 => 'Office',
            3 => 'Other',
            default => 'Unknown',
        };
    }

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
            ->useLogName('address')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Address model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stateDetail()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
