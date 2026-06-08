<?php

namespace Tests\Unit\Services;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\StaffSchedule;
use App\Models\StaffTimeOff;
use App\Models\User;
use App\Services\VetAvailabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VetAvailabilityServiceTest extends TestCase
{
    use DatabaseTransactions;

    private VetAvailabilityService $service;
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
        $this->service = app(VetAvailabilityService::class);
        $this->today = now()->format('Y-m-d');
    }

    public function test_has_shift_on_date_returns_true_when_vet_has_shift()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        $this->assertTrue($this->service->hasShiftOnDate($this->vet->id, $this->today));
    }

    public function test_has_shift_on_date_returns_false_when_no_shift()
    {
        $this->assertFalse($this->service->hasShiftOnDate($this->vet->id, $this->today));
    }

    public function test_has_shift_on_date_returns_false_when_shift_not_vet()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => false,
        ]);

        $this->assertFalse($this->service->hasShiftOnDate($this->vet->id, $this->today));
    }

    public function test_get_available_vets_returns_vet_with_shift_and_free_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'is_vet_shift' => true,
        ]);

        $available = $this->service->getAvailableVets($this->today);

        $this->assertCount(1, $available);
        $this->assertEquals($this->vet->id, $available->first()->id);
    }

    public function test_get_available_vets_excludes_vet_with_no_shift()
    {
        $available = $this->service->getAvailableVets($this->today);

        $this->assertCount(0, $available);
    }

    public function test_get_available_vets_excludes_vet_with_no_free_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '08:30',
            'is_vet_shift' => true,
        ]);

        Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '08:00',
            'duration' => 30,
            'status' => 'scheduled',
        ]);

        $available = $this->service->getAvailableVets($this->today);

        $this->assertCount(0, $available);
    }

    public function test_get_slots_for_vet_returns_empty_when_no_shift()
    {
        $slots = $this->service->getSlotsForVet($this->vet->id, $this->today);

        $this->assertEmpty($slots);
    }

    public function test_get_slots_for_vet_returns_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'is_vet_shift' => true,
        ]);

        $slots = $this->service->getSlotsForVet($this->vet->id, $this->today, 30);

        $this->assertCount(2, $slots);
        $this->assertEquals('08:00', $slots[0]['time']);
        $this->assertEquals('08:30', $slots[1]['time']);
    }

    public function test_get_slots_for_vet_excludes_conflicting_appointments()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'is_vet_shift' => true,
        ]);

        Appointment::factory()->create([
            'vet_id' => $this->vet->id,
            'date' => $this->today,
            'time' => '08:00',
            'duration' => 60,
            'status' => 'confirmed',
        ]);

        $slots = $this->service->getSlotsForVet($this->vet->id, $this->today, 30);

        $slotTimes = array_column($slots, 'time');
        $this->assertNotContains('08:00', $slotTimes);
        $this->assertNotContains('08:30', $slotTimes);
        $this->assertContains('09:00', $slotTimes);
    }

    public function test_get_slots_for_vet_returns_empty_when_on_approved_time_off()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        StaffTimeOff::factory()->create([
            'user_id' => $this->vet->id,
            'start_date' => $this->today,
            'end_date' => $this->today,
            'status' => 'approved',
        ]);

        $slots = $this->service->getSlotsForVet($this->vet->id, $this->today);

        $this->assertEmpty($slots);
    }

    public function test_get_slots_for_vet_returns_slots_when_time_off_pending()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'is_vet_shift' => true,
        ]);

        StaffTimeOff::factory()->create([
            'user_id' => $this->vet->id,
            'start_date' => $this->today,
            'end_date' => $this->today,
            'status' => 'pending',
        ]);

        $slots = $this->service->getSlotsForVet($this->vet->id, $this->today, 30);

        $this->assertNotEmpty($slots);
    }

    public function test_has_available_slots()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'is_vet_shift' => true,
        ]);

        $this->assertTrue($this->service->hasAvailableSlots($this->vet->id, $this->today));
    }

    public function test_is_slot_available()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'is_vet_shift' => true,
        ]);

        $this->assertTrue($this->service->isSlotAvailable($this->vet->id, $this->today, '08:00', 30));
        $this->assertFalse($this->service->isSlotAvailable($this->vet->id, $this->today, '09:00', 30));
    }

    public function test_get_vet_shifts_for_period()
    {
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => $this->today,
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);
        StaffSchedule::factory()->create([
            'user_id' => $this->vet->id,
            'work_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => false,
        ]);

        $shifts = $this->service->getVetShiftsForPeriod(
            $this->vet->id,
            $this->today,
            now()->addDays(5)->format('Y-m-d')
        );

        $this->assertCount(2, $shifts);
    }

    public function test_get_available_vets_returns_empty_when_no_vet_role()
    {
        Role::where('slug', 'veterinario')->delete();

        $available = $this->service->getAvailableVets($this->today);

        $this->assertCount(0, $available);
    }
}
