<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Booking, User, Rides};
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reviews>
 */
class ReviewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ride_id' => Rides::factory(), // Assumes RideFactory exists
            'reviewer_id' => User::factory(), // Assumes UserFactory exists
            'rating' => $this->faker->randomFloat(2, 1, 5), // Ratings between 1 and 5
            'comment' => $this->faker->paragraph,
            'review_date' => now(),
        ];
    }
}
