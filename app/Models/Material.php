<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Material extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'materials';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'last_updated_by',
    ];

    protected $appends = [
        'status_desc',
    ];

    /**
     * Accessor for human-readable status
     */
    public function getStatusDescAttribute()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('material')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Material model has been {$eventName}");
    }

    /**
     * Relationships (if applicable)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
