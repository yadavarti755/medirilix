<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionHistory extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'transaction_histories';

    protected $fillable = [
        'user_id',
        'txn_id',
        'order_number',
        'amount',
        'transaction_date',
        'payment_status',
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
            ->useLogName('transaction_history')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "TransactionHistory model has been {$eventName}");
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
