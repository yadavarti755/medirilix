<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IntendedUse extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'intended_uses';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('intended_uses')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Intended use model has been {$eventName}");
    }
}
