<?php

namespace Tests\Feature\Controllers;

use App\Models\Boarding;
use App\Models\BoardingDailyTask;
use App\Models\BoardingKennel;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class BoardingControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        Boarding::factory()->count(3)->create();
        $response = $this->get(route('boardings.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        Boarding::factory()->create(['status' => 'checked_in']);
        Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->get(route('boardings.index', ['status' => 'checked_in']));
        $response->assertOk();
    }

    public function test_create()
    {
        Pet::factory()->count(2)->create();
        $response = $this->get(route('boardings.create'));
        $response->assertOk();
    }

    public function test_store_creates_boarding()
    {
        $pet = Pet::factory()->create();
        $response = $this->post(route('boardings.store'), [
            'pet_id' => $pet->id,
            'type' => 'boarding',
            'check_in_at' => now()->format('Y-m-d'),
            'expected_check_out' => now()->addDays(3)->format('Y-m-d'),
            'daily_rate' => 80.00,
            'grooming_fee' => 0,
            'reason' => 'Viagem do tutor',
            'feeding_instructions' => 'Ração 3x ao dia',
            'medication_instructions' => 'Antibiótico 1x ao dia',
            'pickup_contact' => '11999999999',
            'notes' => 'Porte médio',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('boardings', [
            'pet_id' => $pet->id,
            'status' => 'checked_in',
            'daily_rate' => 80.00,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('boardings.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'type', 'check_in_at', 'daily_rate']);
    }

    public function test_show()
    {
        $boarding = Boarding::factory()->create();
        $response = $this->get(route('boardings.show', $boarding));
        $response->assertOk();
    }

    public function test_edit_fails_for_checked_out()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->get(route('boardings.edit', $boarding));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_boarding()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_in', 'daily_rate' => 50]);
        $response = $this->put(route('boardings.update', $boarding), [
            'pet_id' => $boarding->pet_id,
            'type' => 'boarding',
            'check_in_at' => now()->format('Y-m-d'),
            'daily_rate' => 100.00,
            'grooming_fee' => 0,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('boardings', [
            'id' => $boarding->id,
            'daily_rate' => 100.00,
        ]);
    }

    public function test_update_fails_for_checked_out()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->put(route('boardings.update', $boarding), [
            'pet_id' => $boarding->pet_id,
            'type' => 'boarding',
            'check_in_at' => now()->format('Y-m-d'),
            'daily_rate' => 100.00,
            'grooming_fee' => 0,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_checkout_changes_status()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_in']);
        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => now()->format('Y-m-d'),
            'total_amount' => 240.00,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('boardings', [
            'id' => $boarding->id,
            'status' => 'checked_out',
            'total_amount' => 240.00,
        ]);
    }

    public function test_checkout_fails_when_not_checked_in()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => now()->format('Y-m-d'),
            'total_amount' => 240.00,
        ]);
        $response->assertSessionHas('error');
    }

    public function test_cancel_changes_status()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_in']);
        $response = $this->post(route('boardings.cancel', $boarding));
        $response->assertRedirect();
        $this->assertDatabaseHas('boardings', [
            'id' => $boarding->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cancel_fails_when_not_checked_in()
    {
        $boarding = Boarding::factory()->create(['status' => 'cancelled']);
        $response = $this->post(route('boardings.cancel', $boarding));
        $response->assertSessionHas('error');
    }

    public function test_store_task_adds_daily_task()
    {
        $boarding = Boarding::factory()->create();
        $response = $this->post(route('boardings.tasks.store', $boarding), [
            'task_date' => now()->format('Y-m-d'),
            'task_name' => 'Administrar medicação',
            'description' => 'Dar antibiótico às 14h',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('boarding_daily_tasks', [
            'boarding_id' => $boarding->id,
            'task_name' => 'Administrar medicação',
        ]);
    }

    public function test_destroy_fails_when_checked_in()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_in']);
        $response = $this->delete(route('boardings.destroy', $boarding));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('boardings', ['id' => $boarding->id]);
    }

    public function test_destroy_deletes_when_not_checked_in()
    {
        $boarding = Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->delete(route('boardings.destroy', $boarding));
        $response->assertRedirect();
        $this->assertDatabaseMissing('boardings', ['id' => $boarding->id]);
    }

    public function test_active()
    {
        Boarding::factory()->create(['status' => 'checked_in']);
        Boarding::factory()->create(['status' => 'checked_out']);
        $response = $this->get(route('boardings.active'));
        $response->assertOk();
    }

    public function test_kennel_map()
    {
        BoardingKennel::factory()->create(['is_active' => true]);
        $response = $this->get(route('boardings.kennel-map'));
        $response->assertOk();
    }

    public function test_assign_kennel()
    {
        $boarding = Boarding::factory()->create();
        $kennel = BoardingKennel::factory()->create();
        $response = $this->post(route('boardings.assign-kennel', $boarding), [
            'kennel_id' => $kennel->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('boardings', [
            'id' => $boarding->id,
            'kennel_id' => $kennel->id,
        ]);
    }
}
