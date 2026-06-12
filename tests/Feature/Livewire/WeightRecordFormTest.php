<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\WeightRecord;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class WeightRecordFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create()
    {
        $pet = Pet::factory()->create();

        Livewire::test('weight-record-form')
            ->set('pet_id', $pet->id)
            ->set('weight', 15.5)
            ->set('measurement_date', '2026-06-01')
            ->set('bcs', 5)
            ->call('save')
            ->assertDispatched('weight-record-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('weight_records', [
            'pet_id' => $pet->id,
            'weight' => 15.5,
            'bcs' => 5,
            'measured_by' => auth()->id(),
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('weight-record-form')
            ->call('save')
            ->assertHasErrors(['pet_id', 'weight', 'measurement_date']);
    }

    public function test_can_edit()
    {
        $record = WeightRecord::factory()->create([
            'weight' => 10.0,
        ]);

        Livewire::test('weight-record-form', ['id' => $record->id])
            ->assertSet('weight', (string) $record->weight)
            ->set('weight', 12.0)
            ->call('save')
            ->assertDispatched('weight-record-saved');

        $this->assertDatabaseHas('weight_records', [
            'id' => $record->id,
            'weight' => 12.0,
        ]);
    }

    public function test_can_edit_via_event()
    {
        $record = WeightRecord::factory()->create([
            'weight' => 8.5,
        ]);

        Livewire::test('weight-record-form')
            ->dispatch('editWeightRecord', id: $record->id)
            ->assertSet('weightRecordId', $record->id)
            ->assertSet('weight', (string) $record->weight)
            ->set('weight', 9.2)
            ->call('save')
            ->assertDispatched('weight-record-saved');

        $this->assertDatabaseHas('weight_records', [
            'id' => $record->id,
            'weight' => 9.2,
        ]);
    }

    public function test_reset_form()
    {
        $record = WeightRecord::factory()->create();

        Livewire::test('weight-record-form')
            ->dispatch('editWeightRecord', id: $record->id)
            ->assertSet('weightRecordId', $record->id)
            ->dispatch('resetForm')
            ->assertSet('weightRecordId', null)
            ->assertSet('pet_id', '')
            ->assertSet('weight', '')
            ->assertSet('bcs', '')
            ->assertSet('measurement_date', '');
    }
}
