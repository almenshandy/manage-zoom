<?php

namespace Database\Factories;

use App\Models\WhatsAppBulkBatch;
use App\Models\WhatsAppBulkMessage;
use App\Models\ZoomBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsAppBulkMessage>
 */
class WhatsAppBulkMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_id' => WhatsAppBulkBatch::factory(),
            'booking_id' => fn (array $attributes): int => ZoomBooking::factory()->create([
                'user_id' => WhatsAppBulkBatch::findOrFail($attributes['batch_id'])->user_id,
            ])->id,
            'topic' => fake()->sentence(),
            'recipient' => '6281234567890',
            'message' => 'Undangan Zoom Meeting',
            'status' => 'queued',
        ];
    }
}
