<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'txn_id',
        'order_number',
        'payment_status',
        'amount',
        'transaction_date',
        'message',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * Configure Spatie activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('transaction')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Transaction model has been {$eventName}");
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
