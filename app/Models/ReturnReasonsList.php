<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ReturnReasonsList extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'return_reasons_lists';

    protected $fillable = [
        'code',
        'text',
    ];

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('return_reason')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ReturnReasonsList model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'return_reason', 'code');
    }
}
