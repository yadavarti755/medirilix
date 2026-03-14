<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\OrderStatus;

class OrderProductList extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'order_product_lists';

    protected $fillable = [
        'user_id',
        'order_number',
        'product_id',
        'product_featured_image',
        'product_name',
        'size',
        'material',
        'price',
        'quantity',
        'total_price',
        'discount_amount',
        'tax_amount',
        'product_order_status',
        'status_changed_date',
        'status_changed_by',
        'remarks',
        'cancel_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'product_order_status' => OrderStatus::class,
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
            ->useLogName('order_product_list')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Order Product List model has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shippingDetail()
    {
        return $this->hasOne(OrderProductShippingDetail::class, 'order_product_list_id');
    }

    public function cancellationRequest()
    {
        return $this->hasOne(OrderCancellationRequest::class, 'order_product_list_id')->latest();
    }

    public function cancellationRequests()
    {
        return $this->hasMany(OrderCancellationRequest::class, 'order_product_list_id')->orderBy('created_at', 'desc');
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class, 'order_product_list_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
