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
        $amount = $this->faker->randomFloat(2, 50, 1000);
        $transactionFee = $this->faker->optional(0.7)->randomFloat(2, 1, 50);
        
        $booking = Booking::inRandomOrder()->first() ?? Booking::factory()->create();

        return [
            'booking_id' => $booking->booking_id,
            'payment_reference' => $this->faker->regexify('[A-Z0-9]{10}'),
            'amount' => $amount,
            'transaction_fee' => $transactionFee,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'SAR', 'GBP', 'AED']),
            'payment_method' => $this->faker->randomElement([
                'Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer', 'Cash',
            ]),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'paid_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function completed()
    {
        return $this->state(fn () => [
            'payment_status' => 'completed',
            'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function failed()
    {
        return $this->state(fn () => [
            'payment_status' => 'failed',
            'paid_at' => null,
        ]);
    }

    public function refunded()
    {
        return $this->state(fn () => [
            'payment_status' => 'refunded',
            'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function forBooking(Booking $booking)
    {
        return $this->state(fn () => [
            'booking_id' => $booking->booking_id,
        ]);
    }
}
