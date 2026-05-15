<?php

namespace Tests\Feature\Controllers;

use App\Models\Department;
use App\Models\Position;
use Tests\ModuleTestCase;

class DepartmentControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Department::factory()->count(3)->create();
        $response = $this->get(route('departments.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('departments.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('departments.store'), [
            'name' => 'Cardiologia',
            'description' => 'Departamento de cardiologia',
        ]);
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Cardiologia']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('departments.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $department = Department::factory()->create();
        $response = $this->get(route('departments.show', $department));
        $response->assertOk();
    }

    public function test_edit()
    {
        $department = Department::factory()->create();
        $response = $this->get(route('departments.edit', $department));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $department = Department::factory()->create(['name' => 'Antigo']);
        $response = $this->put(route('departments.update', $department), [
            'name' => 'Novo Nome',
            'description' => 'Descrição atualizada',
        ]);
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Novo Nome']);
    }

    public function test_destroy_deletes_record()
    {
        $department = Department::factory()->create();
        $response = $this->delete(route('departments.destroy', $department));
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_destroy_fails_with_positions()
    {
        $department = Department::factory()->create();
        Position::factory()->create(['department_id' => $department->id]);
        $response = $this->delete(route('departments.destroy', $department));
        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
