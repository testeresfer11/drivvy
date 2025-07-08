<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User, Rides, Cars};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rides>
 */
class RidesFactory extends Factory
{
    protected $model = Rides::class;

    public function definition()
    {
        return [
            'driver_id' => User::factory(), // Assuming you have 10 users in your database
            'car_id' => Cars::factory(), // Assuming you have 10 cars in your database
            'departure_city' => $this->faker->city,
            'arrival_city' => $this->faker->city,
            'departure_time' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'arrival_time' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'price_per_seat' => $this->faker->randomFloat(2, 20, 200),
            'available_seats' => $this->faker->numberBetween(1, 4),
            'luggage_size' => $this->faker->randomElement(['Small', 'Medium', 'Large']),
            'smoking_allowed' => $this->faker->boolean,
            'pets_allowed' => $this->faker->boolean,
            'music_preference' => $this->faker->randomElement(['Classical', 'Pop', 'Rock', 'Jazz']),
            'description' => $this->faker->text,
        ];
    }
}
