<?php

namespace Tests\Feature;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\ZoomAccount;
use App\Models\ZoomBooking;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BulkWhatsAppPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_full_page_shows_booking_owner_masked_account_and_only_the_recipient_dialog(): void
    {
        $user = User::factory()->create(['name' => '<script>dangerous-owner</script>', 'is_active' => true]);
        $account = ZoomAccount::create(['name' => 'Zoom Host', 'email' => 'host@example.test', 'password' => 'private-zoom-password']);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'zoom_account_id' => $account->id]);
        $this->actingAs($user)->get(route('bookings.whatsapp-bulk.create', ['booking_ids' => [$booking->id]]))
            ->assertOk()->assertViewIs('bookings.bulk-whatsapp-create')->assertSee('Booking atas nama:')
            ->assertSee($user->name)->assertDontSee($user->name, false)->assertSee('Sertakan akun Zoom')
            ->assertSee('bulkRecipientsModal', false)->assertDontSee('bulkWhatsAppModal', false)
            ->assertDontSee('private-zoom-password')->assertSee('host@example.test')
            ->assertViewHas('canIncludeAccounts', true);
    }

    public function test_page_rejects_other_users_bookings_and_guest_access(): void
    {
        $booking = ZoomBooking::factory()->create();
        $url = route('bookings.whatsapp-bulk.create', ['booking_ids' => [$booking->id]]);
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['is_active' => true, 'role' => 2]))->get($url)->assertForbidden();
    }

    public function test_admin_can_prepare_other_users_bookings_and_open_an_empty_history_page(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'role' => 1]);
        $booking = ZoomBooking::factory()->create();
        $this->actingAs($admin)->get(route('bookings.whatsapp-bulk.create', ['booking_ids' => [$booking->id]]))->assertOk()->assertSee($booking->user->name);
        $this->get(route('bookings.whatsapp-bulk.create'))->assertOk()->assertSee('Belum ada meeting dipilih.');
    }

    public function test_opted_in_credentials_are_encrypted_and_owner_is_the_booking_owner(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'role' => 1]);
        $owner = User::factory()->create(['name' => 'Pemohon Meeting']);
        $account = ZoomAccount::create(['name' => 'Zoom Host', 'email' => 'host@example.test', 'password' => 'secret-for-host']);
        $booking = ZoomBooking::factory()->create(['user_id' => $owner->id, 'zoom_account_id' => $account->id]);
        SystemSetting::set('flowkirim_token', 'test-token');
        Queue::fake([SendBulkWhatsAppMessage::class]);
        $this->actingAs($admin)->postJson(route('bookings.whatsapp-bulk.store'), $this->payload($booking, true))->assertAccepted();
        $batch = WhatsAppBulkBatch::firstOrFail();
        $this->assertTrue($batch->include_credentials);
        $message = $batch->messages()->firstOrFail();
        $this->assertStringContainsString('Booking atas nama: Pemohon Meeting', $message->message);
        $this->assertSame('secret-for-host', $message->account_credentials['password']);
        $this->assertStringNotContainsString('secret-for-host', $message->getRawOriginal('account_credentials'));
        $this->assertArrayNotHasKey('account_credentials', $message->toArray());
        $this->getJson(route('bookings.whatsapp-bulk.show', $batch))->assertOk()->assertJsonPath('include_credentials', true)->assertDontSee('secret-for-host');
        Queue::assertPushed(SendBulkWhatsAppMessage::class, 1);
    }

    #[TestWith(['missing'])]
    #[TestWith(['empty_password'])]
    #[TestWith(['inactive'])]
    public function test_unavailable_account_blocks_only_the_opted_in_account_message(string $state): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $account = $state === 'missing' ? null : ZoomAccount::create(['name' => 'Incomplete', 'email' => 'incomplete@example.test', 'password' => $state === 'empty_password' ? null : 'secret', 'status' => $state === 'inactive' ? 'inactive' : 'active']);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'zoom_account_id' => $account?->id]);
        Queue::fake([SendBulkWhatsAppMessage::class]);
        SystemSetting::set('flowkirim_token', 'test-token');
        $this->actingAs($user)->postJson(route('bookings.whatsapp-bulk.store'), $this->payload($booking, true))->assertUnprocessable()->assertJsonValidationErrors('include_credentials');
        $this->assertDatabaseCount('whatsapp_bulk_batches', 0);
        Queue::assertNothingPushed();
        $this->postJson(route('bookings.whatsapp-bulk.store'), $this->payload($booking, false))->assertAccepted();
        $this->assertNull(WhatsAppBulkBatch::firstOrFail()->messages()->firstOrFail()->account_credentials);
        Queue::assertPushed(SendBulkWhatsAppMessage::class, 1);
    }

    public function test_admin_contact_picker_searches_active_profiles_and_normalizes_numbers(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'role' => 1]);
        User::factory()->create(['name' => 'Andi Meeting', 'no_hp' => '081234567890', 'is_active' => true]);
        User::factory()->create(['name' => 'Budi Meeting', 'no_hp' => '081234567891', 'is_active' => true]);
        User::factory()->create(['name' => 'Inactive Meeting', 'no_hp' => '081234567892', 'is_active' => false]);
        User::factory()->create(['name' => 'No Number Meeting', 'no_hp' => null, 'is_active' => true]);
        $this->actingAs($admin)->getJson(route('bookings.whatsapp-bulk.contacts', ['search' => 'Andi']))
            ->assertOk()->assertJsonCount(1, 'contacts')->assertJsonPath('contacts.0.name', 'Andi Meeting')
            ->assertJsonPath('contacts.0.phone', '6281234567890')->assertJsonPath('contacts.0.valid', true);
        $this->getJson(route('bookings.whatsapp-bulk.contacts'))->assertOk()->assertJsonCount(2, 'contacts');
    }

    public function test_regular_users_cannot_read_other_users_contact_numbers(): void
    {
        $user = User::factory()->create(['is_active' => true, 'role' => 2, 'no_hp' => '081234567890']);
        User::factory()->create(['is_active' => true, 'no_hp' => '089999999999']);
        $this->actingAs($user)->getJson(route('bookings.whatsapp-bulk.contacts'))
            ->assertOk()->assertJsonCount(1, 'contacts')->assertJsonPath('contacts.0.id', $user->id)->assertDontSee('089999999999');
    }

    public function test_contacts_are_paginated_and_guest_or_inactive_access_is_rejected(): void
    {
        $this->getJson(route('bookings.whatsapp-bulk.contacts'))->assertUnauthorized();
        $admin = User::factory()->create(['is_active' => true, 'role' => 1]);
        User::factory()->count(21)->create(['is_active' => true, 'no_hp' => '081234567890']);
        $this->actingAs($admin)->getJson(route('bookings.whatsapp-bulk.contacts', ['page' => 2]))->assertOk()->assertJsonCount(1, 'contacts')->assertJsonPath('last_page', 2);
        $admin->update(['is_active' => false]);
        $this->getJson(route('bookings.whatsapp-bulk.contacts'))->assertForbidden();
    }

    private function payload(ZoomBooking $booking, bool $includeCredentials): array
    {
        return ['request_id' => (string) Str::uuid(), 'booking_ids' => [$booking->id], 'recipients' => '081234567890', 'interval_seconds' => 10, 'include_credentials' => $includeCredentials];
    }
}
