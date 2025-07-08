<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Booking, User, Rides};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bookings>
 */
class BookingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ride_id' => Rides::factory(), // Assuming you have 10 rides in your database
            'passenger_id' => User::factory(), // Assuming you have 10 users in your database
            'booking_date' => $this->faker->dateTimeThisMonth(),
            'status' => $this->faker->randomElement(['Pending', 'Confirmed', 'Cancelled']),
            'seat_count' => $this->faker->numberBetween(1, 4),
        ];
    }
}
