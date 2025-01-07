<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,user_id',
            'flight_id' => 'nullable|exists:flights,flight_id',
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'car_id' => 'nullable|exists:cars,car_id',
            'booking_date' => 'required|date|after_or_equal:today',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,paypal',
            'special_requests' => 'nullable|string|max:500',
        ];
    }


    public function messages()
    {
        return [
            'user_id.required' => __('validation.user_id_required'),
            'user_id.exists' => __('validation.user_id_exists'),
            'flight_id.exists' => __('validation.flight_id_exists'),
            'hotel_id.exists' => __('validation.hotel_id_exists'),
            'car_id.exists' => __('validation.car_id_exists'),
            'booking_date.required' => __('validation.booking_date_required'),
            'booking_date.date' => __('validation.booking_date_date'),
            'booking_date.after_or_equal' => __('validation.booking_date_after_or_equal'),
            'total_price.required' => __('validation.total_price_required'),
            'total_price.numeric' => __('validation.total_price_numeric'),
            'total_price.min' => __('validation.total_price_min'),
            'payment_method.required' => __('validation.payment_method_required'),
            'payment_method.in' => __('validation.payment_method_in'),
            'special_requests.string' => __('validation.special_requests_string'),
            'special_requests.max' => __('validation.special_requests_max', ['max' => 500]),
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('payment_method')) {
            $this->merge(['payment_method' => strtolower($this->payment_method)]);
        }
    }
}
