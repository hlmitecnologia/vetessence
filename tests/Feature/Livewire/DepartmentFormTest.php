<?php

namespace Tests\Feature\Livewire;

use App\Models\Department;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class DepartmentFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('department-form')
            ->set('name', 'Clínica Geral')
            ->call('save')
            ->assertDispatched('department-saved');

        $this->assertDatabaseHas('departments', ['name' => 'Clínica Geral']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('department-form')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_can_edit(): void
    {
        $department = Department::factory()->create(['name' => 'Cirurgia']);

        Livewire::test('department-form')
            ->dispatch('editDepartment', id: $department->id)
            ->assertSet('name', 'Cirurgia')
            ->set('name', 'Cirurgia Geral')
            ->call('save')
            ->assertDispatched('department-saved');

        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Cirurgia Geral']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('department-form')
            ->set('name', 'Temp')
            ->set('description', 'Temp desc')
            ->dispatch('resetForm')
            ->assertSet('name', '')
            ->assertSet('description', '');
    }

    public function test_can_create_with_description(): void
    {
        Livewire::test('department-form')
            ->set('name', 'Cardiologia')
            ->set('description', 'Departamento de cardiologia veterinária')
            ->call('save')
            ->assertDispatched('department-saved');

        $this->assertDatabaseHas('departments', [
            'name' => 'Cardiologia',
            'description' => 'Departamento de cardiologia veterinária',
        ]);
    }
}
