<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Slider extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'category_id',
        'title',
        'subtitle',
        'description',
        'file_name',
        'is_published',
        'created_by',
        'updated_by'
    ];

    protected $appends = ['is_published_desc', 'file_url'];

    public function getIsPublishedDescAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    public function getFileUrlAttribute()
    {
        return $this->file_name ? asset('storage/' . Config::get('file_paths')['SLIDER_IMAGE_PATH'] . '/' . $this->file_name) : null;
    }

    // 📝 Spatie activity log configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // logs all fillable attributes
            ->useLogName('slider') // label for this model in logs
            ->logOnlyDirty() // logs only changed fields
            ->setDescriptionForEvent(fn(string $eventName) => "Slider model has been {$eventName}");
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
