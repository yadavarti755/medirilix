<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SocialMediaPlatform extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('social_media_platform')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Social media platform model has been {$eventName}");
    }
}
