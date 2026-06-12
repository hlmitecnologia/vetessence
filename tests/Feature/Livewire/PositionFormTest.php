<?php

namespace Tests\Feature\Livewire;

use App\Models\Department;
use App\Models\Position;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class PositionFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        $department = Department::factory()->create();

        Livewire::test('position-form')
            ->set('name', 'Veterinário')
            ->set('department_id', (string) $department->id)
            ->call('save')
            ->assertDispatched('position-saved');

        $this->assertDatabaseHas('positions', ['name' => 'Veterinário']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('position-form')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_can_edit(): void
    {
        $position = Position::factory()->create(['name' => 'Auxiliar']);

        Livewire::test('position-form')
            ->dispatch('editPosition', id: $position->id)
            ->assertSet('name', 'Auxiliar')
            ->set('name', 'Auxiliar Veterinário')
            ->call('save')
            ->assertDispatched('position-saved');

        $this->assertDatabaseHas('positions', ['id' => $position->id, 'name' => 'Auxiliar Veterinário']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('position-form')
            ->set('name', 'Temp')
            ->set('description', 'Temp desc')
            ->dispatch('resetForm')
            ->assertSet('name', '')
            ->assertSet('description', '')
            ->assertSet('department_id', '');
    }

    public function test_can_create_without_department(): void
    {
        Livewire::test('position-form')
            ->set('name', 'Recepcionista')
            ->call('save')
            ->assertDispatched('position-saved');

        $this->assertDatabaseHas('positions', ['name' => 'Recepcionista', 'department_id' => null]);
    }
}
