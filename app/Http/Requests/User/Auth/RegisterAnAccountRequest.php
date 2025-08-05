<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAnAccountRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
            'date_of_birth' => 'required|date|before:today',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|string|in:male,female,other',
            'otp' => 'required|integer|digits:6',
        ];
    }
}
