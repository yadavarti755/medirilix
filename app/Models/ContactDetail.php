<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContactDetail extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'address',
        'phone_numbers',
        'email_ids',
        'is_primary',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['is_primary_desc'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('contact_detail')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Contact detail model has been {$eventName}");
    }

    public function getIsPrimaryDescAttribute()
    {
        return $this->is_primary ? 'Yes' : 'No';
    }

    public static function getPrimaryContactDetails()
    {
        return self::where([
            'is_primary' => 1,
        ])->first();
    }
}
