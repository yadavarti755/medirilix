<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCancellationRequestMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_cancellation_request_id',
        'message_by',
        'message',
    ];

    public function request()
    {
        return $this->belongsTo(OrderCancellationRequest::class, 'order_cancellation_request_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'message_by');
    }
}
