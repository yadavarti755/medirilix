<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Config;

class Brand extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'file_name',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return $this->file_name ? asset('storage/' . Config::get('file_paths')['BRAND_IMAGE_PATH'] . '/' . $this->file_name) : null;
    }

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('brands')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Brand model has been {$eventName}");
    }

    /**
     * Relationships (if applicable)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
