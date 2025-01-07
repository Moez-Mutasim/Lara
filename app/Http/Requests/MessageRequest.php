<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,user_id',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|in:info,warning,error',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('validation.user_id_required'),
            'user_id.exists' => __('validation.user_id_exists'),
            'message.required' => __('validation.message_required'),
            'message.string' => __('validation.message_string'),
            'message.max' => __('validation.message_max', ['max' => 1000]),
            'type.in' => __('validation.type_in'),
        ];
    }


    protected function prepareForValidation(): void
    {
        if ($this->has('type')) {
            $this->merge(['type' => strtolower($this->type)]);
        }
    }
    
}
