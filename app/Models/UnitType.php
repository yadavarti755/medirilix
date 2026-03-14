<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UnitType extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'unit_types';

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
            ->useLogName('unit_types')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Unit Type model has been {$eventName}");
    }
}
