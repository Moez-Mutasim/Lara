<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'model' => 'required|integer|min:1900|max:' . date('Y'),
            'brand' => 'required|string|max:255',
            'rental_price' => 'required|numeric|min:0',
            'availability' => 'required|boolean',
            'features' => 'nullable|json',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
