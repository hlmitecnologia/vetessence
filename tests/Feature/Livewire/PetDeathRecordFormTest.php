<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\PetDeathRecord;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class PetDeathRecordFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create()
    {
        $pet = Pet::factory()->create();

        Livewire::test('pet-death-record-form')
            ->set('pet_id', $pet->id)
            ->set('death_date', '2026-05-20')
            ->set('cause', 'natural causes')
            ->call('save')
            ->assertDispatched('pet-death-record-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('pet_death_records', [
            'pet_id' => $pet->id,
            'cause' => 'natural causes',
            'registered_by' => auth()->id(),
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('pet-death-record-form')
            ->call('save')
            ->assertHasErrors(['pet_id', 'death_date']);
    }

    public function test_can_edit()
    {
        $record = PetDeathRecord::factory()->create();

        Livewire::test('pet-death-record-form', ['id' => $record->id])
            ->assertSet('pet_id', (string) $record->pet_id)
            ->set('cause', 'euthanasia')
            ->call('save')
            ->assertDispatched('pet-death-record-saved');

        $this->assertDatabaseHas('pet_death_records', [
            'id' => $record->id,
            'cause' => 'euthanasia',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $record = PetDeathRecord::factory()->create([
            'cause' => 'illness',
        ]);

        Livewire::test('pet-death-record-form')
            ->dispatch('editPetDeathRecord', id: $record->id)
            ->assertSet('petDeathRecordId', $record->id)
            ->assertSet('cause', 'illness')
            ->set('cause', 'old age')
            ->call('save')
            ->assertDispatched('pet-death-record-saved');

        $this->assertDatabaseHas('pet_death_records', [
            'id' => $record->id,
            'cause' => 'old age',
        ]);
    }

    public function test_reset_form()
    {
        $record = PetDeathRecord::factory()->create();

        Livewire::test('pet-death-record-form')
            ->dispatch('editPetDeathRecord', id: $record->id)
            ->assertSet('petDeathRecordId', $record->id)
            ->dispatch('resetForm')
            ->assertSet('petDeathRecordId', null)
            ->assertSet('pet_id', '')
            ->assertSet('death_date', '')
            ->assertSet('cause', '');
    }
}
