<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ----- Required Fields (marked with *) -----
            'category_id'           => 'required|integer|exists:categories,id',
            'name'                  => 'required|string|max:255',
            'mrp'                   => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:0',
            'featured_image'        => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:2048',

            // ----- Optional Fields -----
            'available_quantity'    => 'nullable|integer|min:0',
            'material_id'           => 'nullable|integer|exists:materials,id',
            'description'           => 'nullable|string',
            'meta_keywords'         => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:255',
            'product_listing_type'  => 'nullable|integer',
            'stock_availability'    => 'nullable|integer|in:0,1',
            'is_published'          => 'nullable|integer|in:0,1',

            // Multiple images (optional)
            'multiple_product_image'   => 'nullable|array',
            'multiple_product_image.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'category_id.required'      => 'Please select a category.',
            'category_id.exists'        => 'The selected category does not exist.',
            'name.required'             => 'Product name is required.',
            'mrp.required'              => 'MRP is required.',
            'mrp.numeric'               => 'MRP must be a valid number.',
            'mrp.min'                   => 'MRP must be at least 0.',
            'selling_price.required'    => 'Selling price is required.',
            'selling_price.numeric'     => 'Selling price must be a valid number.',
            'selling_price.min'         => 'Selling price must be at least 0.',
            'quantity.required'         => 'Quantity is required.',
            'quantity.integer'          => 'Quantity must be an integer.',
            'quantity.min'              => 'Quantity must be at least 0.',
            'featured_image.required'   => 'Featured image is required.',
            'featured_image.mimes'      => 'Featured image must be jpg, jpeg, png, gif, or webp.',
            'featured_image.max'        => 'Featured image must not exceed 2MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
