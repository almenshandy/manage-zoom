<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ZoomBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ZoomBooking>
 */
class ZoomBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'topic' => fake()->sentence(),
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => ZoomBooking::STATUS_APPROVED,
        ];
    }
}
