<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_product_list_id',
        'return_list_id',
        'return_description',
        'return_status',
        'return_pickup_details',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProductList()
    {
        return $this->belongsTo(OrderProductList::class, 'order_product_list_id');
    }

    public function returnReason()
    {
        return $this->belongsTo(ReturnReason::class, 'return_list_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
