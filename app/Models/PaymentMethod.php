<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PaymentMethod extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'image',
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
        return $this->image ? asset('storage/' . Config::get('file_paths')['PAYMENT_METHOD_IMAGE_PATH'] . '/' . $this->image) : null;
    }

    // 📝 Spatie activity log configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // logs all fillable attributes
            ->useLogName('payment_method') // label for this model in logs
            ->logOnlyDirty() // logs only changed fields
            ->setDescriptionForEvent(fn(string $eventName) => "PaymentMethod model has been {$eventName}");
    }
}
