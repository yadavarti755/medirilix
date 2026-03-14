<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Feedback extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'mobile_no',
        'message',
    ];

    protected $appends = ['created_at_formatted'];
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('feedback')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Feedback model has been {$eventName}");
    }
}
