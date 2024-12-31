<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->user->user_id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'nullable|in:guest,customer,admin',
        ];
    }
}
