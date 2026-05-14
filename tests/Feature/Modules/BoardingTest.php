<?php

namespace Tests\Feature\Modules;

use App\Models\Boarding;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class BoardingTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('boardings.index'));
        $response->assertOk();
    }

    public function test_checkin_creates_boarding()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->post(route('boardings.store'), [
            'pet_id' => $pet->id,
            'type' => 'boarding',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
            'daily_rate' => 50.00,
            'reason' => 'Viagem do tutor',
        ]);
        $response->assertRedirect(route('boardings.index'));
        $this->assertDatabaseHas('boardings', ['pet_id' => $pet->id, 'status' => 'checked_in']);
    }

    public function test_checkout_changes_status()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id, 'status' => 'checked_in',
            'check_in_at' => now()->subDays(2),
            'daily_rate' => 50,
            'created_by' => auth()->id(),
        ]);

        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => now()->format('Y-m-d\TH:i'),
            'total_amount' => 100.00,
        ]);
        $response->assertRedirect();
        $this->assertEquals('checked_out', $boarding->fresh()->status);
    }

    public function test_daily_task_flow()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id, 'status' => 'checked_in',
            'check_in_at' => now(), 'daily_rate' => 50,
            'created_by' => auth()->id(),
        ]);

        $this->post(route('boardings.tasks.store', $boarding), [
            'task_name' => 'Alimentação',
            'task_date' => now()->format('Y-m-d'),
        ]);
        $this->assertDatabaseHas('boarding_daily_tasks', ['boarding_id' => $boarding->id]);
    }

    public function test_cancel_boarding()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id, 'status' => 'checked_in',
            'check_in_at' => now(), 'daily_rate' => 50,
            'created_by' => auth()->id(),
        ]);

        $this->post(route('boardings.cancel', $boarding));
        $this->assertEquals('cancelled', $boarding->fresh()->status);
    }

    public function test_active_scope()
    {
        Boarding::factory()->create(['status' => 'checked_in']);
        Boarding::factory()->create(['status' => 'checked_out']);
        $this->assertEquals(1, Boarding::active()->count());
    }
}
