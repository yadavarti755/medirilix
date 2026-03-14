<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuLocation extends Model
{
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Menu location model has been {$eventName}");
    }
}
