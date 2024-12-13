<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'payment_reference' => $this->faker->regexify('[A-Z0-9]{10}'),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_method' => $this->faker->randomElement([
                'Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer', 'Cash'
            ]),
            'payment_status' => 'pending',
        ];
    }

    
    public function completed()
    {return $this->state(fn () => ['payment_status' => 'completed']);}

    
    public function failed()
    {return $this->state(fn () => ['payment_status' => 'failed']);}

    
    public function forBooking(Booking $booking)
    {return $this->state(fn () => ['booking_id' => $booking->id]);}
}
