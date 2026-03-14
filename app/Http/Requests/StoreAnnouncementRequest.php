<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAnnouncementRequest extends FormRequest
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
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'title_hi' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_hi' => ['nullable', 'string'],
            'file_or_link' => ['required', 'in:file,link'],
            'page_link' => ['nullable', 'url'],
            'status' => ['required'],
            'file_name' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx', 'max:2048'],
            'file_name_hi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx', 'max:2048'],
        ];

        // Conditional validation
        if ($this->input('file_or_link') === 'file') {
            $rules['file_name'] = ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx', 'max:2048'];
        } elseif ($this->input('file_or_link') === 'link') {
            $rules['page_link'] = ['required', 'url'];
        }

        return $rules;
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
