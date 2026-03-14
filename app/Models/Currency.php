<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Currency extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'currencies';

    protected $fillable = [
        'currency',
        'symbol',
        'amount_in_dollars',
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
            ->useLogName('currencies')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Currency model has been {$eventName}");
    }
}
