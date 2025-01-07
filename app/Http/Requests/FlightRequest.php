<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'departure_id' => 'required|exists:locations,location_id',
            'destination_id' => 'required|exists:locations,location_id',
            'airline_name' => 'required|string|max:255',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'class' => 'required|in:Economy,Business,First',
            'seats_available' => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
        ];
    }
}
