<?php

namespace Tests\Feature;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\ZoomBooking;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class BulkWhatsAppQueuePageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_queue_list_is_separate_from_the_send_form_and_linked_from_bookings(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->actingAs($user)->get(route('bookings.whatsapp-bulk.index'))->assertOk()
            ->assertViewIs('bookings.bulk-whatsapp-index')->assertSee('Belum ada antrean bulk.')
            ->assertDontSee('id="bulkWhatsAppForm"', false);
        $this->get(route('bookings.whatsapp-bulk.create'))->assertOk()
            ->assertSee(route('bookings.whatsapp-bulk.index'))->assertDontSee('id="bulkHistoryList"', false);
        $this->get(route('bookings.index'))->assertOk()->assertSee(route('bookings.whatsapp-bulk.index'));
    }

    public function test_all_owned_batches_are_paginated_and_a_selected_older_batch_opens_on_its_page(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $batches = WhatsAppBulkBatch::factory()->count(16)->create(['user_id' => $user->id]);
        $other = WhatsAppBulkBatch::factory()->create();
        $response = $this->actingAs($user)->get(route('bookings.whatsapp-bulk.index'))->assertOk();
        $page = $response->viewData('bulkBatches');
        $this->assertSame(16, $page->total());
        $this->assertCount(15, $page->items());
        $this->assertSame($batches->last()->id, $page->items()[0]->id);
        $response->assertDontSee('data-bulk-batch-id="'.$other->id.'"', false);
        $secondPage = $this->get(route('bookings.whatsapp-bulk.index', ['page' => 2]))->assertOk()->viewData('bulkBatches');
        $this->assertSame([$batches->first()->id], $secondPage->pluck('id')->all());
        $focused = $this->get(route('bookings.whatsapp-bulk.index', ['batch' => $batches->first()->id]))
            ->assertOk()->assertViewHas('focusedBatchId', $batches->first()->id);
        $this->assertSame(2, $focused->viewData('bulkBatches')->currentPage());
        $focused->assertSee('data-bulk-batch-id="'.$batches->first()->id.'"', false);
    }

    public function test_successful_submission_links_to_the_new_batch_on_the_queue_page(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('flowkirim_token', 'fake-token');
        Queue::fake([SendBulkWhatsAppMessage::class]);
        $response = $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), [
            'request_id' => (string) Str::uuid(), 'booking_ids' => [$booking->id],
            'recipients' => '081234567890', 'interval_seconds' => 10,
        ])->assertAccepted();
        $batch = WhatsAppBulkBatch::firstOrFail();
        $response->assertJsonPath('queue_url', route('bookings.whatsapp-bulk.index', ['batch' => $batch->id]));
        $this->get($response->json('queue_url'))->assertOk()->assertViewHas('focusedBatchId', $batch->id);
        Queue::assertPushed(SendBulkWhatsAppMessage::class, 1);
    }

    public function test_queue_page_rejects_guests_inactive_users_and_another_users_batch(): void
    {
        $this->get(route('bookings.whatsapp-bulk.index'))->assertRedirect(route('login'));
        $user = User::factory()->create(['is_active' => true]);
        $other = WhatsAppBulkBatch::factory()->create();
        $this->actingAs($user)->get(route('bookings.whatsapp-bulk.index', ['batch' => $other->id]))->assertNotFound();
        $this->get(route('bookings.whatsapp-bulk.index', ['batch' => $other->id, 'page' => 2]))->assertNotFound();
        $this->getJson(route('bookings.whatsapp-bulk.index', ['batch' => 'invalid']))->assertUnprocessable()->assertJsonValidationErrors('batch');
        $user->update(['is_active' => false]);
        $this->get(route('bookings.whatsapp-bulk.index'))->assertForbidden();

    }
}
