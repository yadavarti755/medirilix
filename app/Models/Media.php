<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Media extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'file_name',
        'original_name',
        'mime_type',
        'size',
        'alt_text',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['media_public_url'];

    public function getMediaPublicUrlAttribute()
    {
        return asset('storage/' . Config::get('file_paths')['MEDIA_IMAGE_PATH'] . '/' . $this->file_name);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Media model has been {$eventName}");
    }
}
