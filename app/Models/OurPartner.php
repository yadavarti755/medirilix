<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OurPartner extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'file_name',
        'title',
        'link',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['file_name_full_path'];

    public function getFileNameFullPathAttribute()
    {
        return asset('storage/' . Config::get('file_paths')['OUR_PARTNER_IMAGE_PATH'] . '/' . $this->file_name);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('our_partner')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Our partner model has been {$eventName}");
    }
}
