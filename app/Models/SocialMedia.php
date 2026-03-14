<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SocialMedia extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'type',
        'name',
        'url',
        'icon_class',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('social_media')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Social media model has been {$eventName}");
    }

    public function socialMediaPlatform()
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'type', 'id');
    }

    public static function getSocialMediaLinks()
    {
        return self::get();
    }
}
