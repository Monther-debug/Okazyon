<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'rating.required' => __('Rating is required'),
            'rating.integer' => __('Rating must be a number'),
            'rating.min' => __('Rating must be at least 1'),
            'rating.max' => __('Rating must be at most 5'),
            'comment.required' => __('Comment is required'),
            'comment.string' => __('Comment must be text'),
            'comment.max' => __('Comment cannot exceed 1000 characters'),
        ];
    }
}
