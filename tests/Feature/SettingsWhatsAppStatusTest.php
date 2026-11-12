<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SettingsWhatsAppStatusTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith(['demo-session-123'])]
    #[TestWith(['<script id="provider-session">alert(1)</script>&'])]
    public function test_settings_renders_session_as_code_without_interpreting_provider_content_as_html(string $session): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $this->configureFlowKirim('demo-device');
        $this->fakeFlowKirim($session);

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertOk()->assertSee('Token FlowKirim Aktif! Akun: Demo Account (demo@example.test)');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        $this->assertSame($session, $xpath->query('//*[@id="gatewayStatusDesc"]//code')->item(0)->textContent);
        $this->assertSame(0, $xpath->query('//script[@id="provider-session"]')->length);
        $this->assertStringNotContainsString('<code', $xpath->query('//*[@id="gatewayStatusDesc"]')->item(0)->textContent);
        Http::assertSentCount(2);
    }

    public function test_status_check_returns_plain_message_and_a_separate_session(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $this->configureFlowKirim('demo-device');
        $this->fakeFlowKirim('demo-session-123');

        $response = $this->actingAs($admin)->postJson(route('settings.whatsapp-status'), ['provider' => 'flowkirim']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Token FlowKirim Aktif! Akun: Demo Account (demo@example.test)')
            ->assertJsonPath('session_id', 'demo-session-123');
        Http::assertSentCount(2);
    }

    public function test_settings_omits_session_label_when_no_session_is_configured(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $this->configureFlowKirim('');
        $this->fakeFlowKirim('unused-session');

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        $description = $xpath->query('//*[@id="gatewayStatusDesc"]')->item(0);
        $this->assertSame(0, $xpath->query('.//code', $description)->length);
        $this->assertStringNotContainsString('Session:', $description->textContent);
        Http::assertSentCount(1);
    }

    public function test_fonnte_status_still_renders_without_flowkirim_session_details(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        SystemSetting::set('wa_default_provider', 'fonnte');
        SystemSetting::set('fonnte_token', 'test-token');
        Http::preventStrayRequests();
        Http::fake(['https://api.fonnte.com/device' => Http::response(['status' => true])]);

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertOk()->assertSee('Device Fonnte terhubung.');
        Http::assertSentCount(1);
    }

    private function configureFlowKirim(string $device): void
    {
        SystemSetting::set('wa_default_provider', 'flowkirim');
        SystemSetting::set('flowkirim_token', 'test-token');
        SystemSetting::updateOrCreate(['key' => 'flowkirim_session_id'], [
            'value' => $device,
            'group' => 'whatsapp_gateway',
            'type' => 'string',
            'label' => 'FlowKirim Session ID',
        ]);
    }

    private function fakeFlowKirim(string $session): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://scan.flowkirim.com/api/me' => Http::response([
                'success' => true,
                'data' => ['name' => 'Demo Account', 'email' => 'demo@example.test'],
            ]),
            'https://scan.flowkirim.com/api/whatsapp/sessions/demo-device' => Http::response([
                'data' => ['session_id' => $session],
            ]),
        ]);
    }
}
