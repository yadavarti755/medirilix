<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per image
            'images' => 'array|max:5',
        ];
    }
}
