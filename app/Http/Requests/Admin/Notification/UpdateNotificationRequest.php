<?php

namespace App\Http\Requests\Admin\Notification;

use Illuminate\Foundation\Http\FormRequest;
use App\Utility\Enums\NotificationTypeEnum;

class UpdateNotificationRequest extends FormRequest
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
            'target_id' => ['nullable', 'integer', 'exists:users,id'],
            'en_title' => ['nullable', 'string', 'max:255'],
            'ar_title' => ['nullable', 'string', 'max:255'],
            'en_body' => ['nullable', 'string'],
            'ar_body' => ['nullable', 'string'],
            'target_type' => ['nullable', 'string', 'in:' . implode(',', NotificationTypeEnum::getValues())],
            'scheduled_at' => ['nullable', 'date', 'after:now']
        ];
    }
}
