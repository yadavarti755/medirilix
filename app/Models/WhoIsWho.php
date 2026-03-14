<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WhoIsWho extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'image',
        'order',
        'name',
        'name_hi',
        'designation',
        'designation_hi',
        'mobile_number',
        'email_id',
        'division_id',
        'address',
        'address_hi',
        'show_on_homepage',
        'hide_on_who_is_who',
        'is_approved',
        'is_published',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['is_approved_desc', 'is_published_desc', 'image_full_path', 'hide_on_who_is_who_desc', 'show_on_homepage_desc'];

    public function getImageFullPathAttribute()
    {
        return asset('storage/' . Config::get('file_paths')['WHO_IS_WHO_IMAGE_PATH'] . '/' . $this->image);
    }

    public function getIsPublishedDescAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    public function getIsApprovedDescAttribute()
    {
        if ($this->is_approved == 1) {
            return 'Approved';
        } elseif ($this->is_approved == 2) {
            return 'Rejected';
        } else {
            return 'Pending';
        }
    }

    // 📝 Spatie activity log configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // logs all fillable attributes
            ->useLogName('who_is_who') // label for this model in logs
            ->logOnlyDirty() // logs only changed fields
            ->setDescriptionForEvent(fn(string $eventName) => "Who Is Who model has been {$eventName}");
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function getHideOnWhoIsWhoDescAttribute()
    {
        return $this->hide_on_who_is_who ? 'Yes' : 'No';
    }
    public function getShowOnHomepageDescAttribute()
    {
        return $this->show_on_homepage ? 'Yes' : 'No';
    }
}
