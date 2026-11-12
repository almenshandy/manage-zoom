<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\ZoomAccount;
use App\Models\ZoomBooking;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProjectMeetingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_project_list_has_a_meetings_button_with_the_matching_count(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::create(['name' => 'Proyek Rapat']);
        $this->createBooking($admin, $project);

        $response = $this->actingAs($admin)->get(route('projects.index'));

        $response->assertOk()
            ->assertSee('data-url="'.route('projects.meetings', $project).'"', false)
            ->assertSee('Lihat 1 meeting terkait proyek Proyek Rapat');
    }

    #[TestWith([ZoomBooking::STATUS_PENDING])]
    #[TestWith([ZoomBooking::STATUS_APPROVED])]
    #[TestWith([ZoomBooking::STATUS_REJECTED])]
    #[TestWith([ZoomBooking::STATUS_EXECUTED])]
    #[TestWith([ZoomBooking::STATUS_COMPLETED])]
    #[TestWith([ZoomBooking::STATUS_CANCELLED])]
    public function test_admin_sees_only_meetings_for_the_selected_project_including_all_statuses(int $status): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::create(['name' => 'Proyek Rapat']);
        $otherProject = Project::create(['name' => 'Proyek Rapat Lanjutan']);
        $account = ZoomAccount::create([
            'name' => 'Akun Ruang A',
            'email' => 'ruanga@example.test',
            'password' => 'private-password',
        ]);
        $booking = $this->createBooking($admin, $project, [
            'topic' => '<script>alert("meeting")</script>',
            'status' => $status,
            'zoom_account_id' => $account->id,
        ]);
        $this->createBooking($admin, $otherProject, ['topic' => 'Meeting proyek lain']);

        $response = $this->actingAs($admin)->get(route('projects.meetings', $project));

        $response->assertOk()->assertViewIs('projects.meetings')
            ->assertSee($booking->topic)
            ->assertSee($booking->status_label)
            ->assertSee($admin->name)
            ->assertSee('Akun Ruang A')->assertSee('ruanga@example.test')
            ->assertSee('09:00 - 10:00 WIB')
            ->assertSee(route('bookings.show', $booking))
            ->assertDontSee($booking->topic, false)
            ->assertDontSee('Meeting proyek lain')
            ->assertDontSee('private-password');
        $this->assertSame([$booking->id], $response->viewData('bookings')->modelKeys());
    }

    public function test_project_without_meetings_shows_an_empty_state(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::create(['name' => 'Proyek Baru']);

        $response = $this->actingAs($admin)->get(route('projects.meetings', $project));

        $response->assertOk()->assertSee('Belum ada meeting terkait proyek ini.');
    }

    public function test_booking_without_a_zoom_account_is_still_listed(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::create(['name' => 'Proyek Baru']);
        $this->createBooking($admin, $project);

        $response = $this->actingAs($admin)->get(route('projects.meetings', $project));

        $response->assertOk()->assertSee('Belum dialokasikan');
    }

    #[TestWith([1, 10, 10])]
    #[TestWith([2, 1, 0])]
    public function test_meetings_are_paginated_with_the_newest_schedule_first(int $page, int $count, int $firstIndex): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::create(['name' => 'Proyek Banyak Meeting']);
        $bookings = [];
        for ($day = 1; $day <= 11; $day++) {
            $bookings[] = $this->createBooking($admin, $project, [
                'start_date' => sprintf('2026-11-%02d', $day),
                'end_date' => sprintf('2026-11-%02d', $day),
            ]);
        }

        $response = $this->actingAs($admin)->get(route('projects.meetings', ['project' => $project, 'page' => $page]));

        $response->assertOk()->assertSee('data-meetings-pagination', false);
        $paginator = $response->viewData('bookings');
        $this->assertSame(11, $paginator->total());
        $this->assertCount($count, $paginator->items());
        $this->assertSame($bookings[$firstIndex]->id, $paginator->first()->id);
    }

    public function test_unknown_project_returns_not_found(): void
    {
        $admin = User::factory()->create(['role' => 1]);

        $response = $this->actingAs($admin)->get(route('projects.meetings', 999999));

        $response->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $project = Project::create(['name' => 'Proyek Privat']);

        $response = $this->get(route('projects.meetings', $project));

        $response->assertRedirect(route('login'));
    }

    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    public function test_non_admin_cannot_read_project_meetings(int $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $project = Project::create(['name' => 'Proyek Privat']);

        $response = $this->actingAs($user)->get(route('projects.meetings', $project));

        $response->assertForbidden();
    }

    private function createBooking(User $user, Project $project, array $attributes = []): ZoomBooking
    {
        return ZoomBooking::create(array_merge([
            'user_id' => $user->id,
            'project_name' => $project->name,
            'topic' => 'Rapat Koordinasi',
            'start_date' => '2026-11-10',
            'end_date' => '2026-11-10',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => ZoomBooking::STATUS_PENDING,
        ], $attributes));
    }
}
