<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCancellationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_product_list_id',
        'user_id',
        'cancel_reason_id',
        'description',
        'status',
        'status_changed_by',
        'created_by',
        'updated_by',
    ];

    public function cancelReason()
    {
        return $this->belongsTo(CancelReason::class, 'cancel_reason_id');
    }

    public function messages()
    {
        return $this->hasMany(OrderCancellationRequestMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderProductList()
    {
        return $this->belongsTo(OrderProductList::class, 'order_product_list_id');
    }

    public function statusChangedBy()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }
}
