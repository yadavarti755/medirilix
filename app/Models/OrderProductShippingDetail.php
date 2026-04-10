<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class OrderProductShippingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_product_list_id',
        'shipment_photo',
        'shipping_details',
        'dhl_tracking_id',
        'created_by',
        'updated_by'
    ];

    protected $appends = ['shipment_photo_full_path'];

    public function getShipmentPhotoFullPathAttribute()
    {
        return $this->shipment_photo ? asset('storage/' . Config::get('file_paths')['SHIPMENT_IMAGE_PATH'] . '/' . $this->shipment_photo) : null;
    }


    public function orderProductList()
    {
        return $this->belongsTo(OrderProductList::class, 'order_product_list_id');
    }
}
