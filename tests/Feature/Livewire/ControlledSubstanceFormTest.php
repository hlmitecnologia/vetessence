<?php

namespace Tests\Feature\Livewire;

use App\Models\ControlledSubstance;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ControlledSubstanceFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('controlled-substance-form')
            ->set('name', 'Metadona')
            ->set('schedule', 'A1')
            ->set('unit', 'mg/ml')
            ->set('current_stock', '100')
            ->call('save')
            ->assertDispatched('controlled-substance-saved');

        $this->assertDatabaseHas('controlled_substances', ['name' => 'Metadona']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('controlled-substance-form')
            ->call('save')
            ->assertHasErrors(['name', 'schedule', 'unit', 'current_stock']);
    }

    public function test_can_edit(): void
    {
        $substance = ControlledSubstance::create([
            'name' => 'Morfina',
            'schedule' => 'A1',
            'unit' => 'mg/ml',
            'current_stock' => 50,
        ]);

        Livewire::test('controlled-substance-form')
            ->dispatch('editControlledSubstance', id: $substance->id)
            ->assertSet('name', 'Morfina')
            ->set('current_stock', '75')
            ->call('save')
            ->assertDispatched('controlled-substance-saved');

        $this->assertDatabaseHas('controlled_substances', ['id' => $substance->id, 'current_stock' => 75]);
    }

    public function test_reset_form(): void
    {
        Livewire::test('controlled-substance-form')
            ->set('name', 'Tramadol')
            ->set('schedule', 'B1')
            ->dispatch('resetForm')
            ->assertSet('name', '')
            ->assertSet('schedule', '')
            ->assertSet('unit', '')
            ->assertSet('current_stock', '')
            ->assertSet('is_active', true);
    }

    public function test_validates_current_stock_numeric(): void
    {
        Livewire::test('controlled-substance-form')
            ->set('name', 'Teste')
            ->set('schedule', 'C1')
            ->set('unit', 'comprimido')
            ->set('current_stock', 'abc')
            ->call('save')
            ->assertHasErrors(['current_stock']);
    }
}
