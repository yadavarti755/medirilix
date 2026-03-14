<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_product_list_id' => 'required|exists:order_product_lists,id',
            'return_list_id' => 'required|exists:return_reasons,id',
            'return_description' => 'nullable|string',
        ];
    }
}
