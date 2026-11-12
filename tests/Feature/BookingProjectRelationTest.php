<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\ZoomAccount;
use App\Models\ZoomBooking;
use App\Services\BulkWhatsAppService;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BookingProjectRelationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_booking_stores_the_selected_project_id_and_a_server_supplied_name_snapshot(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['name' => 'Proyek Sebenarnya']);
        $payload = $this->payload($project->id);
        $payload['project_name'] = 'Nama dari klien harus diabaikan';

        $this->actingAs($user)->post(route('bookings.store'), $payload)->assertRedirect(route('bookings.index'));

        $booking = ZoomBooking::sole();
        $this->assertSame($project->id, $booking->project_id);
        $this->assertSame($project->id, $booking->project->id);
        $this->assertSame('Proyek Sebenarnya', $booking->project_name);
        $this->assertSame([$booking->id], $project->bookings()->pluck('id')->all());
    }

    #[TestWith(['store', 999999])]
    #[TestWith(['update', 999999])]
    #[TestWith(['store', 'Nama Proyek'])]
    #[TestWith(['update', 'Nama Proyek'])]
    public function test_booking_requests_reject_invalid_project_ids(string $action, int|string $projectId): void
    {
        $user = User::factory()->create();
        $payload = $this->payload(null);
        $payload['project_id'] = $projectId;
        $this->actingAs($user);
        if ($action === 'store') {
            $response = $this->postJson(route('bookings.store'), $payload);
        } else {
            $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'status' => ZoomBooking::STATUS_PENDING]);
            $response = $this->putJson(route('bookings.update', $booking), $payload);
        }

        $response->assertUnprocessable()->assertJsonValidationErrors('project_id');
    }

    public function test_changing_the_project_moves_the_relationship_and_removing_it_clears_the_snapshot(): void
    {
        $user = User::factory()->create();
        $first = Project::factory()->create();
        $second = Project::factory()->create();
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'project_id' => $first->id, 'status' => ZoomBooking::STATUS_PENDING]);

        $this->actingAs($user)->put(route('bookings.update', $booking), $this->payload($second->id))->assertRedirect(route('bookings.index'));

        $this->assertSame(0, $first->bookings()->count());
        $this->assertSame(1, $second->bookings()->count());
        $this->assertSame($second->name, $booking->fresh()->project_name);
        $this->put(route('bookings.update', $booking), $this->payload(null))->assertRedirect(route('bookings.index'));
        $this->assertNull($booking->fresh()->project_id);
        $this->assertNull($booking->fresh()->project_name);
    }

    public function test_renaming_a_project_keeps_meetings_attached_and_displays_the_current_name(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::factory()->create(['name' => 'Nama Proyek Lama']);
        $booking = ZoomBooking::factory()->create(['user_id' => $admin->id, 'project_id' => $project->id]);

        $this->actingAs($admin)->put(route('projects.update', $project), ['name' => 'Nama Proyek Baru', 'status' => 'active'])->assertRedirect();

        $this->assertSame(1, $project->bookings()->count());
        $this->get(route('projects.meetings', $project))->assertOk()->assertSee($booking->topic);
        $this->get(route('bookings.index'))->assertOk()->assertSee('Nama Proyek Baru')->assertDontSee('Nama Proyek Lama');
        $this->get(route('bookings.show', $booking))->assertOk()->assertSee('Nama Proyek Baru')->assertDontSee('Nama Proyek Lama');
        $booking->refresh();
        $this->assertSame('Nama Proyek Lama', $booking->project_name);
        $this->assertSame('Nama Proyek Baru', $booking->project_display_name);
        $invitation = app(BulkWhatsAppService::class)->invitation($booking);
        $this->assertStringContainsString('Proyek: Nama Proyek Baru', $invitation);
        $this->assertStringNotContainsString('Nama Proyek Lama', $invitation);
    }

    public function test_project_deletion_preserves_the_booking_and_its_historical_name(): void
    {
        $project = Project::factory()->create(['name' => 'Proyek Dihapus']);
        $booking = ZoomBooking::factory()->create(['project_id' => $project->id]);

        $project->delete();

        $booking->refresh();
        $this->assertNull($booking->project_id);
        $this->assertNull($booking->project);
        $this->assertSame('Proyek Dihapus', $booking->project_display_name);
        $this->assertDatabaseHas('zoom_bookings', ['id' => $booking->id, 'project_name' => 'Proyek Dihapus']);
    }

    #[TestWith(['inactive'])]
    #[TestWith(['completed'])]
    public function test_edit_form_preserves_an_existing_project_that_is_no_longer_active(string $status): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['status' => $status]);
        $otherInactive = Project::factory()->create(['status' => 'inactive']);
        $booking = ZoomBooking::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);

        $response = $this->actingAs($user)->get(route('bookings.edit', $booking))->assertOk();

        $response->assertSee('name="project_id"', false)->assertDontSee('name="project_name"', false);
        $this->assertContains($project->id, $response->viewData('projects')->modelKeys());
        $this->assertNotContains($otherInactive->id, $response->viewData('projects')->modelKeys());
        $this->put(route('bookings.update', $booking), $this->payload($project->id))->assertRedirect(route('bookings.index'));
        $this->assertSame($project->id, $booking->fresh()->project_id);
    }

    public function test_legacy_project_names_are_kept_but_do_not_create_relationships_by_name(): void
    {
        $user = User::factory()->create();
        $first = Project::factory()->create(['name' => 'Nama Sama']);
        $second = Project::factory()->create(['name' => 'Nama Sama']);
        $legacy = ZoomBooking::factory()->create(['user_id' => $user->id, 'project_name' => 'Nama Sama']);
        ZoomBooking::factory()->create(['user_id' => $user->id, 'project_id' => $first->id]);

        $this->actingAs($user)->get(route('bookings.edit', $legacy))->assertOk()->assertSee('Nama proyek lama: Nama Sama');
        $this->put(route('bookings.update', $legacy), $this->payload(null))->assertRedirect(route('bookings.index'));

        $this->assertNull($legacy->fresh()->project_id);
        $this->assertSame('Nama Sama', $legacy->fresh()->project_display_name);
        $this->assertSame(1, $first->bookings()->count());
        $this->assertSame(0, $second->bookings()->count());
    }

    public function test_changing_a_loaded_relationship_uses_the_new_project_and_snapshot(): void
    {
        $first = Project::factory()->create();
        $second = Project::factory()->create();
        $booking = ZoomBooking::factory()->create(['project_id' => $first->id]);
        $booking->load('project');

        $booking->update(['project_id' => $second->id]);

        $this->assertSame($second->id, $booking->project->id);
        $this->assertSame($second->name, $booking->project_name);
    }

    public function test_booking_without_a_project_remains_supported(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload(null);
        unset($payload['project_id']);

        $this->actingAs($user)->post(route('bookings.store'), $payload)->assertRedirect(route('bookings.index'));

        $booking = ZoomBooking::sole();
        $this->assertNull($booking->project_id);
        $this->assertNull($booking->project_display_name);
    }

    public function test_create_form_submits_ids_and_the_availability_matrix_shows_the_current_project_name(): void
    {
        $this->freezeTime();
        $admin = User::factory()->create(['role' => 1]);
        $project = Project::factory()->create(['name' => 'Proyek Sebelum Rename']);
        $account = ZoomAccount::create(['name' => 'Akun Matrix', 'email' => 'matrix@example.test', 'expired_date' => now()->addYear()->toDateString()]);
        $booking = ZoomBooking::factory()->create(['user_id' => $admin->id, 'project_id' => $project->id, 'zoom_account_id' => $account->id]);
        $project->update(['name' => 'Proyek Sesudah Rename']);

        $response = $this->actingAs($admin)->get(route('bookings.create'))->assertOk();

        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $options = (new DOMXPath($document))->query('//select[@name="project_id"]/option[@value="'.$project->id.'"]');
        $this->assertSame(1, $options->length);
        $this->assertStringContainsString('Proyek Sesudah Rename', $options->item(0)->textContent);
        $entry = $response->viewData('matrixBookings')[$account->id][$booking->start_date->toDateString()][0];
        $this->assertSame('Proyek Sesudah Rename', $entry['project_name']);
        $this->assertStringContainsString('Proyek Sesudah Rename', $entry['display_title']);
    }

    private function payload(?int $projectId): array
    {
        return ['topic' => 'Rapat Relasi Proyek', 'project_id' => $projectId, 'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(), 'start_time' => '09:00', 'end_time' => '10:00', 'duration_minutes' => 60];
    }
}
