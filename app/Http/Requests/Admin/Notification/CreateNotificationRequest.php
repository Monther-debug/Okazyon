<?php

namespace App\Http\Requests\Admin\Notification;

use Illuminate\Foundation\Http\FormRequest;
use App\Utility\Enums\NotificationStatusEnum;
use App\Utility\Enums\NotificationTypeEnum;

class CreateNotificationRequest extends FormRequest
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
            'target_id' => ['nullable', 'required_if:target_type,specific_user', 'integer', 'exists:users,id'],
            'en_title' => ['required', 'string', 'max:255'],
            'ar_title' => ['required', 'string', 'max:255'],
            'en_body' => ['required', 'string'],
            'ar_body' => ['required', 'string'],
            'target_type' => ['required', 'string', 'in:' . implode(',', NotificationTypeEnum::cases())],
            'scheduled_at' => ['nullable', 'date', 'after:now']
        ];
    }
}
