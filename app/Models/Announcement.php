<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Announcement extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'file_or_link',
        'file_name',
        'page_link',
        'status',
        'is_published',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['status_desc', 'is_published_desc', 'file_url', 'file_url_hi'];

    public function getIsPublishedDescAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    public function getStatusDescAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_or_link == 'file' && $this->file_name) {
            return asset('storage/' . Config::get('file_paths')['ANNOUNCEMENT_FILE_EN_PATH'] . '/' . $this->file_name);
        }
        return '';
    }

    public function getFileUrlHiAttribute()
    {
        if ($this->file_or_link == 'file' && $this->file_name_hi) {
            return asset('storage/' . Config::get('file_paths')['ANNOUNCEMENT_FILE_HI_PATH'] . '/' . $this->file_name_hi);
        }
        return '';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('announcement')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Announcement model has been {$eventName}");
    }
}
