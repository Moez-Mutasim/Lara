<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidFlightDuration;

class FlightRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration' => ['required', new ValidFlightDuration()],
            'price' => 'required|numeric|min:0',
            'departure_time' => 'required|date|before:arrival_time',
            'arrival_time' => 'required|date|after:departure_time',
            'departure' => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:departure',
        ];
    }

    public function messages(): array
    {
        return [
            'duration.required' => 'The flight duration is required.',
            'price.required' => 'The flight price is required.',
            'price.numeric' => 'The price must be a numeric value.',
            'price.min' => 'The price must be at least 0.',
            'departure_time.before' => 'The departure time must be before the arrival time.',
            'arrival_time.after' => 'The arrival time must be after the departure time.',
            'departure.required' => 'The departure location is required.',
            'destination.required' => 'The destination is required.',
            'destination.different' => 'The destination must be different from the departure location.',
        ];
    }
}
