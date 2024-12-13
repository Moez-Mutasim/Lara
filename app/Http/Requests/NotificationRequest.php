<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return auth()->check();
    }

   
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:500',
            'is_read' => 'nullable|boolean',
            'type' => 'nullable|string|in:info,warning,error',
        ];
    }

    
    public function messages(): array
    {
        return [
            'user_id.required' => __('validation.user_id_required'),
            'user_id.exists' => __('validation.user_id_exists'),
            'message.required' => __('validation.message_required'),
            'message.string' => __('validation.message_string'),
            'message.max' => __('validation.message_max', ['max' => 500]),
            'is_read.boolean' => __('validation.is_read_boolean'),
            'type.in' => __('validation.type_in'),
        ];
    }

    
    public function attributes(): array
    {
        return [
            'user_id' => __('fields.user_id'),
            'message' => __('fields.message'),
            'is_read' => __('fields.is_read'),
            'type' => __('fields.type'),
        ];
    }

    
    protected function prepareForValidation(): void
    {
        if ($this->has('is_read')) {
            $this->merge(['is_read' => filter_var($this->is_read, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
