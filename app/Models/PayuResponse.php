<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PayuResponse extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'payu_responses';

    protected $fillable = [
        'user_id',
        'order_number',
        'mihpayid',
        'mode',
        'status',
        'unmappedstatus',
        'key',
        'txnid',
        'amount',
        'cardcategory',
        'discount',
        'net_amount_debit',
        'addedon',
        'productinfo',
        'firstname',
        'lastname',
        'email',
        'phone',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'zipcode',
        'payment_source',
        'pg_type',
        'bank_ref_num',
        'bankcode',
        'error',
        'error_message',
        'name_on_card',
        'cardnum',
        'message',
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
            ->useLogName('payu_response')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "PayU Response model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
