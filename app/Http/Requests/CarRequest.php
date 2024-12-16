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
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'rental_price' => 'required|numeric|min:0',
            'availability' => 'required|boolean',
            'features' => 'nullable|array',
            'branch_id' => 'required|exists:branches,branch_id',
        ];
    }
}
