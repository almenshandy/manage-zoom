<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ZoomAccount;
use App\Models\ZoomBooking;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BookingAvailabilityTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith(['2026-11-09', '2026-11-12'])]
    #[TestWith(['2026-11-10', '2026-11-12'])]
    #[TestWith(['2026-11-09', '2026-12-15'])]
    public function test_matrix_keeps_later_bookings_visible_after_mapping_an_ongoing_booking(string $startDate, string $endDate): void
    {
        $this->travelTo(Carbon::parse('2026-11-10 09:00:00'));
        $admin = User::factory()->create(['role' => 1]);
        $account = ZoomAccount::create([
            'name' => 'Akun Rapat',
            'email' => 'rapat@example.test',
            'expired_date' => '2026-12-31',
        ]);
        $otherAccount = ZoomAccount::create([
            'name' => 'Akun Pelatihan',
            'email' => 'pelatihan@example.test',
            'expired_date' => '2026-12-31',
        ]);
        $ongoing = $this->createBooking($admin, $account, $startDate, $endDate);
        $sameDay = $this->createBooking($admin, $account, '2026-11-10', '2026-11-10');
        $otherBooking = $this->createBooking($admin, $otherAccount, '2026-11-10', '2026-11-11');
        $laterBooking = $this->createBooking($admin, $account, '2026-12-31', '2027-01-02');

        $response = $this->actingAs($admin)->get(route('bookings.create'));

        $response->assertOk()->assertViewIs('bookings.create');
        $matrix = $response->viewData('matrixBookings');
        $this->assertSame([$ongoing->id, $sameDay->id], array_column($matrix[$account->id]['2026-11-10'] ?? [], 'id'));
        $this->assertSame([$otherBooking->id], array_column($matrix[$otherAccount->id]['2026-11-10'] ?? [], 'id'));
        $this->assertSame([$otherBooking->id], array_column($matrix[$otherAccount->id]['2026-11-11'] ?? [], 'id'));
        $this->assertSame('2026-11-10', $response->viewData('dates')[0]['date_string']);
        $this->assertSame('2026-12-10', $response->viewData('dates')[30]['date_string']);
        $this->assertSame('2026-11-10', array_key_first($matrix[$account->id]));
        $this->assertSame(min($endDate, '2026-12-10'), array_key_last($matrix[$account->id]));
        $mobile = $response->viewData('mobileBookings');
        $this->assertSame([$ongoing->id, $sameDay->id, $laterBooking->id], array_column($mobile[$account->id], 'id'));
        $this->assertSame('2026-11-10', $mobile[$account->id][0]['start_date']);
        $this->assertSame('2026-12-31', $mobile[$account->id][2]['start_date']);
        $this->assertSame('2026-12-31', $mobile[$account->id][2]['end_date']);
    }

    public function test_unauthenticated_request_redirects_to_login(): void
    {
        $response = $this->get(route('bookings.create'));

        $response->assertRedirect(route('login'));
    }

    private function createBooking(User $user, ZoomAccount $account, string $startDate, string $endDate): ZoomBooking
    {
        return ZoomBooking::create([
            'user_id' => $user->id,
            'zoom_account_id' => $account->id,
            'topic' => fake()->sentence(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => ZoomBooking::STATUS_APPROVED,
        ]);
    }
}
