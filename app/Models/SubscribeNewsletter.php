<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SubscribeNewsletter extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'subscribe_newsletters';

    protected $fillable = [
        'email_id'
    ];

    /**
     * Configure Spatie activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('subscribe_newsletter')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "SubscribeNewsletter model has been {$eventName}");
    }
}
