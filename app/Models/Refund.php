<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_product_list_id',
        'refund_amount',
        'refund_status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'refund_status' => \App\Enums\OrderStatus::class,
    ];

    public function orderProductList()
    {
        return $this->belongsTo(OrderProductList::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
