<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Country extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'countries';

    protected $fillable = [
        'name',
        'iso2',
        'phone_code',
        'currency',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Activity Log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'iso2', 'phone_code', 'currency'])
            ->useLogName('country')
            ->logOnlyDirty()
            ->setDescriptionForEvent(
                fn(string $eventName) => "Country record has been {$eventName}"
            );
    }
}
