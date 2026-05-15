<?php

namespace Tests\Feature\Integrations;

use App\Models\Boarding;
use App\Models\BoardingKennel;
use App\Models\Branch;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class BoardingFlowTest extends ModuleTestCase
{
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
    }

    public function test_full_boarding_lifecycle()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $kennel = BoardingKennel::factory()->create([
            'name' => 'Canil A',
            'size' => 'large',
            'capacity' => 2,
            'is_active' => true,
        ]);

        $checkinResponse = $this->post(route('boardings.store'), [
            'pet_id' => $pet->id,
            'type' => 'boarding',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
            'daily_rate' => 50.00,
            'grooming_fee' => 0,
            'reason' => 'Viagem do tutor',
            'branch_id' => $this->branch->id,
        ]);
        $checkinResponse->assertSessionDoesntHaveErrors();
        $checkinResponse->assertRedirect(route('boardings.index'));

        $this->assertDatabaseHas('boardings', [
            'pet_id' => $pet->id,
            'status' => 'checked_in',
            'daily_rate' => 50.00,
        ]);
        $boarding = Boarding::where('pet_id', $pet->id)->first();

        $assignResponse = $this->post(route('boardings.assign-kennel', $boarding), [
            'kennel_id' => $kennel->id,
        ]);
        $assignResponse->assertSessionDoesntHaveErrors();

        $taskResponse = $this->post(route('boardings.tasks.store', $boarding), [
            'task_name' => 'Alimentação',
            'task_date' => now()->format('Y-m-d'),
            'description' => 'Ração Premium 3x ao dia',
            'branch_id' => $this->branch->id,
        ]);
        $taskResponse->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('boarding_daily_tasks', [
            'boarding_id' => $boarding->id,
            'task_name' => 'Alimentação',
        ]);

        $checkoutResponse = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => now()->format('Y-m-d\TH:i'),
            'total_amount' => 100.00,
        ]);
        $checkoutResponse->assertSessionDoesntHaveErrors();
        $checkoutResponse->assertRedirect();

        $boarding->refresh();
        $this->assertEquals('checked_out', $boarding->status);
        $this->assertEquals(100.00, $boarding->total_amount);
        $this->assertNotNull($boarding->check_out_at);
    }
}
