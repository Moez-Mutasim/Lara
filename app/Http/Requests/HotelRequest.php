<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:locations,location_id',
            'rating' => 'nullable|numeric|between:1,5',
            'price_per_night' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'availability' => 'required|boolean',
        ];
    }
}
