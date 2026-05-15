<?php

namespace Tests\Feature\Integrations;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class PatientFlowBoardTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->vet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_patient_flow_board_complete_waiting_room_flow()
    {
        $pet = Pet::factory()->create();

        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $this->vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'scheduled',
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'scheduled',
        ]);

        $consultResponse = $this->patch(route('appointments.update-status', $appointment), [
            'status' => 'in_progress',
        ]);
        $consultResponse->assertOk();
        $consultResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'in_progress',
        ]);

        $doneResponse = $this->patch(route('appointments.update-status', $appointment), [
            'status' => 'completed',
        ]);
        $doneResponse->assertOk();
        $doneResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);

        $flowDataResponse = $this->get(route('appointments.flow-data'));
        $flowDataResponse->assertOk();
    }
}
