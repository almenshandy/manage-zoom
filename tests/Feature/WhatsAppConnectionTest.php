<?php

namespace Tests\Feature;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\WhatsAppBulkMessage;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppConnectionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_network_failure_has_a_readable_error_and_does_not_resend_automatically(): void
    {
        $this->freezeTime();
        $user = User::factory()->create(['is_active' => true]);
        $batch = WhatsAppBulkBatch::factory()->create(['user_id' => $user->id]);
        $message = WhatsAppBulkMessage::factory()->create(['batch_id' => $batch->id]);
        SystemSetting::set('wa_gateway_enabled', '1');
        SystemSetting::set('flowkirim_token', 'fake-token');
        SystemSetting::set('flowkirim_endpoint', 'https://scan.flowkirim.com/api/whatsapp/messages/text');
        Http::preventStrayRequests();
        Http::fake(['https://scan.flowkirim.com/api/whatsapp/messages/text' => Http::failedConnection('cURL error 7: Could not connect to server')]);
        $job = (new SendBulkWhatsAppMessage($message->id, now()->addDay()->timestamp))->withFakeQueueInteractions();
        $job->handle(app(WhatsAppService::class));
        $job->handle(app(WhatsAppService::class));

        $message->refresh();
        $this->assertSame('failed', $message->status);
        $this->assertStringContainsString('Koneksi ke server FlowKirim gagal.', $message->error);
        $this->assertStringNotContainsString('cURL', $message->error);
        $this->assertSame(now()->getTimestampMs() + 10000, (int) DB::table('whatsapp_bulk_dispatch_state')->value('next_allowed_at_ms'));
        $this->actingAs($user)->getJson(route('bookings.whatsapp-bulk.show', $batch))->assertOk()
            ->assertJsonPath('failed', 1)->assertJsonPath('pending', 0)->assertDontSee('cURL');
        Http::assertSentCount(1);
    }
}
