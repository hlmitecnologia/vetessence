<?php

namespace Tests\Feature\Controllers;

use App\Models\Department;
use App\Models\Position;
use Tests\ModuleTestCase;

class PositionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Position::factory()->count(3)->create();
        $response = $this->get(route('positions.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('positions.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $department = Department::factory()->create();
        $response = $this->post(route('positions.store'), [
            'name' => 'Cirurgião',
            'description' => 'Responsável por cirurgias',
            'department_id' => $department->id,
        ]);
        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseHas('positions', ['name' => 'Cirurgião']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('positions.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $position = Position::factory()->create();
        $response = $this->get(route('positions.show', $position));
        $response->assertOk();
    }

    public function test_edit()
    {
        $position = Position::factory()->create();
        $response = $this->get(route('positions.edit', $position));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create();
        $response = $this->put(route('positions.update', $position), [
            'name' => 'Novo Cargo',
            'department_id' => $department->id,
        ]);
        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'name' => 'Novo Cargo']);
    }

    public function test_destroy_deletes_record()
    {
        $position = Position::factory()->create();
        $response = $this->delete(route('positions.destroy', $position));
        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }
}
