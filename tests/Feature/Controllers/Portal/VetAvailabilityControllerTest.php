<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\StaffSchedule;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VetAvailabilityControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $vet;
    private Tutor $tutor;
    private string $today;

    protected function setUp(): void
    {
        parent::setUp();

        $vetRole = Role::firstOrCreate(['slug' => 'veterinario'], ['name' => 'Veterinário']);
        $this->vet = User::factory()->create([
            'role_id' => $vetRole->id,
            'is_active' => true,
            'name' => 'Dr. Pet',
            'crmv' => '12345',
        ]);

        $this->tutor = Tutor::factory()->create(['password' => bcrypt('password')]);
        $this->actingAs($this->tutor, 'tutor');

        $this->today = now()->format('Y-m-d');
    }

    public function test_available_vets_returns_vets_with_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'is_vet_shift' => true,
        ]);

        $response = $this->getJson(route('portal.vet-availability.available-vets', ['date' => $this->today]));

        $response->assertOk()
            ->assertJsonCount(1, 'vets')
            ->assertJsonPath('vets.0.name', 'Dr. Pet')
            ->assertJsonPath('vets.0.crmv', '12345');
    }

    public function test_available_vets_requires_date_after_or_equal_today()
    {
        $response = $this->getJson(route('portal.vet-availability.available-vets', ['date' => '2020-01-01']));

        $response->assertStatus(422);
    }

    public function test_vet_slots_returns_slots_for_vet()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'is_vet_shift' => true,
        ]);

        $response = $this->getJson(route('portal.vet-availability.vet-slots', [
            'vet_id' => $this->vet->id,
            'date' => $this->today,
        ]));

        $response->assertOk()
            ->assertJsonCount(2, 'slots');
    }

    public function test_vet_slots_requires_valid_vet()
    {
        $response = $this->getJson(route('portal.vet-availability.vet-slots', [
            'vet_id' => 99999,
            'date' => $this->today,
        ]));

        $response->assertStatus(422);
    }

    public function test_vet_dates_returns_dates_with_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'is_vet_shift' => true,
        ]);

        $response = $this->getJson(route('portal.vet-availability.vet-dates', [
            'vet_id' => $this->vet->id,
        ]));

        $response->assertOk();
        $dates = $response->json('dates');
        $this->assertNotEmpty($dates);
        $this->assertEquals($this->today, $dates[0]['date']);
    }

    public function test_vet_dates_returns_empty_for_non_vet_user()
    {
        $nonVetRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $nonVet = User::factory()->create(['role_id' => $nonVetRole->id]);

        $response = $this->getJson(route('portal.vet-availability.vet-dates', [
            'vet_id' => $nonVet->id,
        ]));

        $response->assertOk()
            ->assertJson(['dates' => []]);
    }

    public function test_vet_dates_requires_vet_id()
    {
        $response = $this->getJson(route('portal.vet-availability.vet-dates'));

        $response->assertStatus(422);
    }

    public function test_available_vets_requires_authentication()
    {
        auth()->logout();

        $response = $this->getJson(route('portal.vet-availability.available-vets', ['date' => $this->today]));

        $response->assertUnauthorized();
    }
}
