<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderProductShippingDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_list_id' => 'required',
            'order_status' => 'required',
            'shipment_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'shipment_details' => 'nullable|string',
            'dhl_tracking_id' => 'nullable|string',
        ];
    }
}
