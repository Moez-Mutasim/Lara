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
            'user_id' => 'nullable|exists:users,id',
            'message' => 'required|string|max:500',
            'is_read' => 'nullable|boolean',
            'type' => 'nullable|string|in:info,warning,error',
        ];
    }
}
