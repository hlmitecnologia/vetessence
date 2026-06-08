<?php

namespace Tests\Unit\Observers;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\StaffSchedule;
use App\Models\User;
use App\Services\VetAvailabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffScheduleObserverTest extends TestCase
{
    use DatabaseTransactions;

    private User $vet;
    private string $today;

    protected function setUp(): void
    {
        parent::setUp();

        $vetRole = Role::firstOrCreate(['slug' => 'veterinario'], ['name' => 'Veterinário']);
        $this->vet = User::factory()->create([
            'role_id' => $vetRole->id,
            'is_active' => true,
        ]);
        $this->today = now()->format('Y-m-d');
    }

    public function test_deleted_non_vet_shift_does_not_cancel_appointments()
    {
        $schedule = StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => false,
        ]);

        $appointment = Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '09:00',
            'status' => 'scheduled',
        ]);

        $schedule->delete();

        $this->assertEquals('scheduled', $appointment->fresh()->status);
    }

    public function test_deleted_vet_shift_cancels_conflicting_appointments()
    {
        $schedule = StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '09:00',
            'status' => 'scheduled',
        ]);

        $schedule->delete();

        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }

    public function test_updated_vet_shift_cancels_conflicting_appointments()
    {
        $schedule = StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '10:00',
            'status' => 'confirmed',
        ]);

        $schedule->update(['start_time' => '12:00']);

        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }

    public function test_updated_vet_shift_does_not_cancel_when_still_available()
    {
        $schedule = StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '11:00',
            'status' => 'scheduled',
        ]);

        $schedule->update(['shift_type' => 'overtime']);

        $this->assertEquals('scheduled', $appointment->fresh()->status);
    }

    public function test_updated_non_vet_shift_does_not_cancel_appointments()
    {
        $schedule = StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => false,
        ]);

        $appointment = Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '09:00',
            'status' => 'scheduled',
        ]);

        $schedule->update(['start_time' => '10:00']);

        $this->assertEquals('scheduled', $appointment->fresh()->status);
    }
}
