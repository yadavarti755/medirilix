<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InitiatePaymentRequest extends FormRequest
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
            'payment_gateway' => 'required|exists:payment_gateways,id',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        // For website frontend (not API), we might want standard redirect back with errors.
        // However, the original controller was doing a redirect back manually for errors in logic,
        // but standard Laravel validation redirects automatically.
        // If this is an AJAX call, we might want JSON.
        // The original controller method `initiatePayment` seems to be a form submission (POST).
        // Standard Laravel behavior is fine here (redirect back with errors).
        // But to be safe and consistent with "Strict" project rules which often favor explicit behavior or JSON for APIs,
        // let's check if it expects JSON. The original code didn't check for AJAX.
        // So we will stick to standard default failedValidation which redirects back.
        parent::failedValidation($validator);
    }
}
