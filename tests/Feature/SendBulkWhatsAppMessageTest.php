<?php

namespace Tests\Feature;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\WhatsAppBulkMessage;
use App\Models\ZoomAccount;
use App\Models\ZoomBooking;
use App\Services\BulkWhatsAppService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SendBulkWhatsAppMessageTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith([7])]
    #[TestWith([10])]
    #[TestWith([15])]
    public function test_shared_interval_survives_cache_clear_and_backlog(int $interval): void
    {
        $this->freezeTime();
        $this->fakeGateway();
        $first = $this->message(['interval_seconds' => $interval]);
        $second = $this->message(['interval_seconds' => $interval]);
        $this->runMessage($first)->assertNotReleased();
        $this->assertSame('sent', $first->fresh()->status);
        Cache::flush();
        $this->runMessage($second)->assertReleased($interval);
        $this->travel($interval - 1)->seconds();
        $this->runMessage($second)->assertReleased(1);
        Http::assertSentCount(1);
        $this->travel(1)->seconds();
        $this->runMessage($second)->assertNotReleased();
        $this->runMessage($second)->assertNotReleased();
        Http::assertSentCount(2);
        $this->assertSame('sent', $second->fresh()->status);
    }

    public function test_reserved_lane_prevents_a_second_worker_from_sending(): void
    {
        $this->freezeTime();
        $this->fakeGateway();
        $message = $this->message();
        DB::table('whatsapp_bulk_dispatch_state')->where('id', 1)->update(['next_allowed_at_ms' => now()->getTimestampMs() + 75000, 'reservation_token' => Str::uuid()]);
        $this->runMessage($message)->assertReleased(15);
        $this->assertSame('queued', $message->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_overdue_jobs_still_send_one_at_a_time_after_gateway_response(): void
    {
        $this->freezeTime();
        $this->fakeGateway();
        Http::fake(['https://scan.flowkirim.com/api/whatsapp/messages/text' => function () {
            $this->travel(5)->seconds();

            return Http::response(['success' => true]);
        }]);
        $messages = [$this->message(), $this->message(), $this->message()];
        $this->travel(1)->hours();
        $this->runMessage($messages[0]);
        $this->runMessage($messages[1])->assertReleased(10);
        $this->runMessage($messages[2])->assertReleased(10);
        Http::assertSentCount(1);
        $this->travel(10)->seconds();
        $this->runMessage($messages[1]);
        $this->runMessage($messages[2])->assertReleased(10);
        Http::assertSentCount(2);
    }

    #[TestWith(['flowkirim'])]
    #[TestWith(['fonnte'])]
    public function test_gateway_rejection_is_recorded_without_automatic_resend(string $provider): void
    {
        $this->freezeTime();
        $this->fakeGateway(false);
        $message = $this->message(['provider' => $provider]);
        $this->runMessage($message);
        $this->runMessage($message);
        $this->assertSame('failed', $message->fresh()->status);
        $this->assertNotEmpty($message->fresh()->error);
        $this->assertSame(now()->getTimestampMs() + 10000, (int) DB::table('whatsapp_bulk_dispatch_state')->value('next_allowed_at_ms'));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request): bool => ($request['to'] ?? $request['target']) === ($provider === 'flowkirim' ? $message->recipient.'@s.whatsapp.net' : $message->recipient));
    }

    #[TestWith(['cancelled'])]
    #[TestWith(['deleted'])]
    #[TestWith(['inactive'])]
    #[TestWith(['ownership'])]
    #[TestWith(['disabled'])]
    public function test_changed_booking_or_account_state_blocks_sending(string $change): void
    {
        $this->fakeGateway();
        $message = $this->message();
        match ($change) {
            'cancelled' => $message->booking->update(['status' => 4]),
            'deleted' => $message->booking->delete(),
            'inactive' => $message->batch->user->update(['is_active' => false]),
            'ownership' => $message->booking->update(['user_id' => User::factory()->create()->id]),
            'disabled' => SystemSetting::set('wa_gateway_enabled', '0'),
        };
        $this->runMessage($message);
        $this->assertSame('failed', $message->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_interrupted_send_requires_manual_review_instead_of_resending(): void
    {
        $this->freezeTime();
        $this->fakeGateway();
        $message = $this->message();
        $message->update(['status' => 'sending', 'attempted_at' => now()->subSeconds(80)]);
        $this->runMessage($message);
        $this->assertSame('unknown', $message->fresh()->status);
        Http::assertNothingSent();
    }

    #[TestWith([false, 'active'])]
    #[TestWith([true, 'active'])]
    #[TestWith([true, 'inactive'])]
    #[TestWith([true, 'maintenance'])]
    public function test_actual_database_queue_processes_the_committed_outbox(bool $includeCredentials, string $status): void
    {
        $this->fakeGateway();
        $user = User::factory()->create(['is_active' => true]);
        $account = ZoomAccount::create(['name' => 'Host Zoom', 'email' => 'host@example.test', 'password' => 'host-password', 'password_hotmail' => 'mail-password', 'client_secret' => 'api-secret', 'status' => $status]);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'zoom_account_id' => $account->id]);
        $batch = app(BulkWhatsAppService::class)->enqueue($user, ['request_id' => (string) Str::uuid(), 'booking_ids' => [$booking->id], 'recipients' => '081234567890', 'interval_seconds' => 10, 'include_credentials' => $includeCredentials]);
        $this->assertDatabaseCount('jobs', 1);
        $this->artisan('queue:work', ['connection' => 'database', '--queue' => 'whatsapp-bulk', '--once' => true, '--sleep' => 0, '--tries' => 0])->assertSuccessful();
        $this->assertSame('sent', $batch->messages()->firstOrFail()->status);
        $this->assertDatabaseCount('jobs', 0);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request): bool => str_contains($request['message'], 'Booking atas nama: '.$user->name)
            && str_contains($request['message'], 'Email: host@example.test') === $includeCredentials
            && str_contains($request['message'], 'Password: host-password') === $includeCredentials
            && ! str_contains($request['message'], 'mail-password') && ! str_contains($request['message'], 'api-secret'));
    }

    #[TestWith(['password'])]
    #[TestWith(['assignment'])]
    #[TestWith(['deleted'])]
    public function test_account_changes_while_queued_block_stale_credentials(string $change): void
    {
        $this->fakeGateway();
        $user = User::factory()->create(['is_active' => true]);
        $account = ZoomAccount::create(['name' => 'Host Zoom', 'email' => 'host@example.test', 'password' => 'host-password']);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'zoom_account_id' => $account->id]);
        Queue::fake([SendBulkWhatsAppMessage::class]);
        $batch = app(BulkWhatsAppService::class)->enqueue($user, ['request_id' => (string) Str::uuid(), 'booking_ids' => [$booking->id], 'recipients' => '081234567890', 'interval_seconds' => 10, 'include_credentials' => true]);
        match ($change) {
            'password' => $account->update(['password' => 'new-password']),
            'assignment' => $booking->update(['zoom_account_id' => null]),
            'deleted' => $account->delete(),
        };
        $message = $batch->messages()->firstOrFail();
        $this->runMessage($message);
        $this->assertSame('failed', $message->fresh()->status);
        Http::assertNothingSent();
        Queue::assertPushed(SendBulkWhatsAppMessage::class, 1);
    }

    private function fakeGateway(bool $success = true): void
    {
        SystemSetting::set('wa_gateway_enabled', '1');
        SystemSetting::set('flowkirim_token', 'test-token');
        SystemSetting::set('fonnte_token', 'test-token');
        SystemSetting::create(['key' => 'flowkirim_session_id', 'value' => '', 'label' => 'Session', 'group' => 'whatsapp', 'type' => 'text']);
        SystemSetting::set('flowkirim_endpoint', 'https://scan.flowkirim.com/api/whatsapp/messages/text');
        Http::preventStrayRequests();
        Http::fake([
            'https://scan.flowkirim.com/api/whatsapp/messages/text' => Http::response(['success' => $success, 'message' => 'Gateway response'], $success ? 200 : 400),
            'https://api.fonnte.com/send' => Http::response(['status' => $success, 'reason' => 'Gateway response'], $success ? 200 : 400),
        ]);
    }

    private function message(array $batchAttributes = []): WhatsAppBulkMessage
    {
        $user = User::factory()->create(['role' => 2, 'is_active' => true]);
        $batch = WhatsAppBulkBatch::factory()->create(['user_id' => $user->id, ...$batchAttributes]);

        return WhatsAppBulkMessage::factory()->create(['batch_id' => $batch->id]);
    }

    private function runMessage(WhatsAppBulkMessage $message): SendBulkWhatsAppMessage
    {
        $job = (new SendBulkWhatsAppMessage($message->id, now()->addDay()->timestamp))->withFakeQueueInteractions();
        $job->handle(app(WhatsAppService::class));

        return $job;
    }
}
