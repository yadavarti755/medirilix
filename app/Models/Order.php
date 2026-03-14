<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Transaction;
use App\Models\OrderProductList;
use App\Models\OrderHistory;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_number',
        'order_date',
        'subtotal_price',
        'additional_charges',
        'total_price',
        'invoice_number',
        'payment_type',
        'order_status',
        'payment_status',
        'remarks',
        'created_by',
        'updated_by',
        'discount_amount',
        'coupon_code',
        'coupon_data',
        'tax_amount',
        'shipping_charges',
    ];

    protected $casts = [
        'order_status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    protected $appends = [
        'order_status_desc',
        'payment_status_desc',
        'status_desc',
    ];

    /**
     * Accessor for human-readable order status
     */
    public function getOrderStatusDescAttribute()
    {
        return $this->order_status instanceof OrderStatus ? $this->order_status->label() : $this->order_status;
    }

    /**
     * Accessor for human-readable payment status
     */
    public function getPaymentStatusDescAttribute()
    {
        return $this->payment_status instanceof PaymentStatus ? $this->payment_status->label() : $this->payment_status;
    }

    /**
     * Accessor for general status description
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
            ->logAll() // logs all fillable attributes
            ->useLogName('order') // label for this model in logs
            ->logOnlyDirty() // only log changed attributes
            ->setDescriptionForEvent(fn(string $eventName) => "Order model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    public function orderProductList() // using default naming convention or explicit if needed
    {
        return $this->hasMany(OrderProductList::class, 'order_number', 'order_number');
    }

    public function orderHistory()
    {
        return $this->hasMany(OrderHistory::class, 'order_number', 'order_number');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_number', 'order_number');
    }
}
