<?php

namespace Tests\Feature;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\WhatsAppBulkMessage;
use App\Models\ZoomBooking;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BulkWhatsAppTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_meetings_are_queued_per_unique_recipient_and_request_retries_are_idempotent(): void
    {
        $this->freezeTime();
        $user = User::factory()->create(['is_active' => true]);
        $bookings = ZoomBooking::factory()->count(2)->create(['user_id' => $user->id]);
        SystemSetting::set('flowkirim_token', 'test-token');
        Queue::fake();
        Http::preventStrayRequests();
        $payload = $this->payload($bookings->modelKeys());
        $payload['recipients'] = '081234567890, +62 81234567890;6282234567890';
        $response = $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $payload);
        $response->assertAccepted()->assertJsonPath('total_messages', 4);
        $this->postJson(route('bookings.whatsapp-bulk.store'), $payload)->assertAccepted()->assertJsonPath('status_url', $response->json('status_url'));
        $this->assertDatabaseCount('whatsapp_bulk_batches', 1);
        $this->assertDatabaseCount('whatsapp_bulk_messages', 4);
        Queue::assertPushed(SendBulkWhatsAppMessage::class, 4);
        Queue::assertPushedOn('whatsapp-bulk', SendBulkWhatsAppMessage::class, fn ($job): bool => $job->connection === 'database' && $job->afterCommit === true);
        $this->assertSame([0, 10, 20, 30], Queue::pushed(SendBulkWhatsAppMessage::class)->map(fn (SendBulkWhatsAppMessage $job): int => (int) now()->diffInSeconds($job->delay))->all());
        Http::assertNothingSent();
    }

    #[TestWith([7])]
    #[TestWith([15])]
    public function test_interval_boundaries_are_accepted(int $interval): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('flowkirim_token', 'test-token');
        Queue::fake();
        $payload = $this->payload([$booking->id]);
        $payload['interval_seconds'] = $interval;
        $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $payload)->assertAccepted();
        $this->assertSame($interval, WhatsAppBulkBatch::firstOrFail()->interval_seconds);
    }

    #[TestWith(['interval_seconds', 6])]
    #[TestWith(['interval_seconds', 16])]
    #[TestWith(['recipients', 'abc'])]
    #[TestWith(['recipients', '123'])]
    #[TestWith(['booking_ids', []])]
    public function test_invalid_input_creates_no_queue(string $field, mixed $value): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id]);
        Queue::fake();
        $payload = $this->payload([$booking->id]);
        $payload[$field] = $value;
        $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('whatsapp_bulk_batches', 0);
        Queue::assertNothingPushed();
    }

    #[TestWith([0])]
    #[TestWith([2])]
    #[TestWith([4])]
    #[TestWith([5])]
    public function test_ineligible_meetings_cannot_be_sent(int $status): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'status' => $status]);
        Queue::fake();
        $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $this->payload([$booking->id]))->assertUnprocessable()->assertJsonValidationErrors('booking_ids');
        Queue::assertNothingPushed();
    }

    public function test_ownership_gateway_and_private_progress_are_enforced(): void
    {
        $user = User::factory()->create(['is_active' => true, 'role' => 2]);
        $otherBooking = ZoomBooking::factory()->create();
        Queue::fake();
        $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $this->payload([$otherBooking->id]))->assertForbidden();
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('wa_gateway_enabled', '0');
        $this->postJson(route('bookings.whatsapp-bulk.store'), $this->payload([$booking->id]))->assertUnprocessable()->assertJsonValidationErrors('gateway');
        $batch = WhatsAppBulkBatch::factory()->create();
        $this->getJson(route('bookings.whatsapp-bulk.show', $batch))->assertNotFound();
        Queue::assertNothingPushed();
    }

    public function test_admin_can_queue_another_users_meeting_without_host_credentials_and_view_progress(): void
    {
        $admin = User::factory()->create(['role' => 1, 'is_active' => true]);
        $booking = ZoomBooking::factory()->create(['join_url' => 'https://zoom.us/j/123', 'zoom_meeting_id' => '123', 'passcode' => 'participant-code']);
        SystemSetting::set('flowkirim_token', 'test-token');
        Queue::fake();
        $this->actingAs($admin)->postJson(route('bookings.whatsapp-bulk.store'), $this->payload([$booking->id]))->assertAccepted();
        $batch = WhatsAppBulkBatch::firstOrFail();
        $message = $batch->messages()->firstOrFail();
        $this->assertStringContainsString('https://zoom.us/j/123', $message->message);
        $this->assertStringContainsString('participant-code', $message->message);
        $this->assertStringNotContainsString('Password:', $message->message);
        $this->assertStringNotContainsString('Email:', $message->message);
        $this->getJson(route('bookings.whatsapp-bulk.show', $batch))->assertOk()->assertJsonPath('pending', 1)->assertJsonPath('accepted', 0)->assertJsonCount(1, 'messages');
        $this->get(route('bookings.index'))->assertOk()->assertSee('Kirim Bulk WhatsApp')->assertDontSee('bulkWhatsAppModal', false);
    }

    public function test_guest_cannot_queue_messages(): void
    {
        $this->postJson(route('bookings.whatsapp-bulk.store'), $this->payload([1]))->assertUnauthorized();
    }

    public function test_progress_counts_all_messages_while_paginating_details(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $batch = WhatsAppBulkBatch::factory()->create(['user_id' => $user->id, 'total_messages' => 51]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id]);
        WhatsAppBulkMessage::factory()->count(50)->sequence(fn ($sequence): array => ['recipient' => '62812'.str_pad((string) $sequence->index, 8, '0', STR_PAD_LEFT)])
            ->create(['batch_id' => $batch->id, 'booking_id' => $booking->id]);
        WhatsAppBulkMessage::factory()->create(['batch_id' => $batch->id, 'booking_id' => $booking->id, 'recipient' => '6289999999999', 'status' => 'sent']);
        $this->actingAs($user)->getJson(route('bookings.whatsapp-bulk.show', ['batch' => $batch, 'page' => 2]))
            ->assertOk()->assertJsonPath('pending', 50)->assertJsonPath('accepted', 1)->assertJsonPath('last_page', 2)
            ->assertJsonCount(1, 'messages')->assertJsonPath('messages.0.recipient', '6289999999999');
    }

    private function payload(array $bookingIds): array
    {
        return ['request_id' => (string) Str::uuid(), 'booking_ids' => $bookingIds, 'recipients' => '081234567890', 'interval_seconds' => 10];
    }
}
