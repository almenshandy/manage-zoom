<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsAppBulkBatch>
 */
class WhatsAppBulkBatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => fake()->uuid(),
            'user_id' => User::factory(),
            'provider' => 'flowkirim',
            'interval_seconds' => 10,
            'total_messages' => 1,
        ];
    }
}
