<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Bookings, User, Rides};


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\messages>
 */
class MessagesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ride_id' => Rides::factory(),
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'content' => $this->faker->paragraph,
            'timestamp' => now(),
        ];
    }
}
