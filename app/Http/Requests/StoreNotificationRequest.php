<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id', // Nullable for system-wide notifications
            'message' => 'required|string|max:500',
            'type' => 'nullable|string|in:info,warning,error',
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => __('The selected user does not exist.'),
            'message.required' => __('A message is required for the notification.'),
            'message.string' => __('The message must be a string.'),
            'message.max' => __('The message cannot exceed 500 characters.'),
            'type.in' => __('The type must be one of the following: info, warning, or error.'),
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => __('User'),
            'message' => __('Notification Message'),
            'type' => __('Notification Type'),
        ];
    }
}
