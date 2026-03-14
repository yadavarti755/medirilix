<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Config;

class Offer extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'offers';

    protected $fillable = [
        'title',
        'description',
        'image',
        'type',
        'type_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . Config::get('file_paths')['OFFER_IMAGE_PATH'] . '/' . $this->image) : null;
    }

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('offers')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Offer model has been {$eventName}");
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'type_id', 'id');
    }
}
