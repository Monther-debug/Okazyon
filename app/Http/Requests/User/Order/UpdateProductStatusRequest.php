<?php

namespace App\Http\Requests\User\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStatusRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'status' => 'required|in:pending,confirmed,preparing,ready,shipped,delivered,cancelled'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => __('Product ID is required'),
            'product_id.exists' => __('The selected product does not exist'),
            'status.required' => __('Status is required'),
            'status.in' => __('The selected status is invalid'),
        ];
    }
}
