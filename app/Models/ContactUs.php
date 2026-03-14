<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContactUs extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'contact_us';

    protected $fillable = [
        'name',
        'email_id',
        'phone_number',
        'message',
        'status', // 0: Pending/Inactive, 1: Active/Replied? - Assumed from controller usage
        'created_by',
        'updated_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('contact_us')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Contact Us query has been {$eventName}");
    }
}
