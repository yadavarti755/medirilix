<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class State extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'country_id',
        'name',
        'iso2',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['country_id', 'name', 'iso2'])
            ->useLogName('state')
            ->logOnlyDirty()
            ->setDescriptionForEvent(
                fn(string $eventName) => "State record has been {$eventName}"
            );
    }
}
