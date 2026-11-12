<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ZoomAccount;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ZoomAccountCredentialsTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith(['"><script id="injected">alert(1)</script>&\''])]
    #[TestWith(['0'])]
    public function test_admin_sees_masked_password_and_can_access_safely_encoded_copy_data(string $password): void
    {
        $admin = User::factory()->create(['role' => 1]);
        ZoomAccount::create([
            'name' => 'Akun Zoom Rapat',
            'email' => 'rapat@example.test',
            'password' => $password,
            'client_secret' => 'private-api-secret',
            'password_hotmail' => 'private-email-password',
        ]);

        $response = $this->actingAs($admin)->get(route('zoom-accounts.index'));

        $response->assertOk()->assertSee('Password')->assertSee('Salin Akun');
        $response->assertDontSee('private-api-secret')->assertDontSee('private-email-password');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        $row = $xpath->query('//tr[@data-account-email="rapat@example.test"]')->item(0);
        $this->assertSame($password, $row->getAttribute('data-account-password'));
        $mobileCard = $xpath->query('//article[@data-zoom-account and @data-account-email="rapat@example.test"]')->item(0);
        $this->assertSame($password, $mobileCard->getAttribute('data-account-password'));
        $this->assertSame('********', $xpath->query('.//span[contains(@class, "account-password")]', $mobileCard)->item(0)->textContent);
        $this->assertSame('********', $xpath->query('.//span[contains(@class, "account-password")]', $row)->item(0)->textContent);
        $this->assertSame('false', $xpath->query('.//button[@aria-controls]', $row)->item(0)->getAttribute('aria-pressed'));
        $this->assertSame(0, $xpath->query('//script[@id="injected"]')->length);
    }

    #[TestWith([null])]
    #[TestWith([''])]
    public function test_account_without_password_displays_an_empty_state(?string $password): void
    {
        $admin = User::factory()->create(['role' => 1]);
        ZoomAccount::create([
            'name' => 'Akun Tanpa Password',
            'email' => 'kosong@example.test',
            'password' => $password,
        ]);

        $response = $this->actingAs($admin)->get(route('zoom-accounts.index'));

        $response->assertOk()->assertSee('Belum diisi')->assertDontSee('aria-controls="account-password-', false);
    }

    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    public function test_non_admin_cannot_access_account_credentials(int $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->actingAs($user)->get(route('zoom-accounts.index'));

        $response->assertForbidden();
    }
}
