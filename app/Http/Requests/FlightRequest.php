<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'departure_id' => [
                'sometimes',
                'required',
                'exists:locations,location_id',
                'different:destination_id',
            ],
            'destination_id' => 'sometimes|required|exists:locations,location_id',
            'airline_name' => 'sometimes|required|string|max:255',
            'departure_time' => 'sometimes|required|date',
            'arrival_time' => 'sometimes|required|date|after:departure_time',
            'price' => 'sometimes|required|numeric|min:0',
            'class' => 'sometimes|required|in:Economy,Business,First',
            'seats_available' => 'sometimes|required|integer|min:0',
            'is_available' => 'sometimes|nullable|boolean',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'departure_id.required' => 'The departure location is required.',
            'departure_id.exists' => 'The selected departure location does not exist.',
            'departure_id.different' => 'The departure and destination locations must be different.',
            'destination_id.required' => 'The destination location is required.',
            'destination_id.exists' => 'The selected destination location does not exist.',
            'arrival_time.after' => 'The arrival time must be after the departure time.',
            'class.in' => 'The class must be one of: Economy, Business, First.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'price' => round($this->price, 2),
            'airline_name' => trim($this->airline_name),
            'class' => ucfirst(strtolower($this->class)),
        ]);
    }
}
