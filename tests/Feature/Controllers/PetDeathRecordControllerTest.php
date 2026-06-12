<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\PetDeathRecord;
use Tests\ModuleTestCase;

class PetDeathRecordControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        PetDeathRecord::factory()->count(3)->create();
        $response = $this->get(route('pet-death-records.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('pet-death-records.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $pet = Pet::factory()->create();

        $response = $this->post(route('pet-death-records.store'), [
            'pet_id' => $pet->id,
            'death_date' => now()->format('Y-m-d'),
            'cause' => 'natural causes',
            'disposition' => 'cremation',
        ]);

        $response->assertRedirect(route('pet-death-records.index'));
        $this->assertDatabaseHas('pet_death_records', [
            'pet_id' => $pet->id,
            'cause' => 'natural causes',
            'registered_by' => auth()->id(),
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('pet-death-records.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'death_date']);
    }

    public function test_show()
    {
        $record = PetDeathRecord::factory()->create();
        $response = $this->get(route('pet-death-records.show', $record));
        $response->assertOk();
    }

    public function test_edit()
    {
        $record = PetDeathRecord::factory()->create();
        $response = $this->get(route('pet-death-records.edit', $record));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $record = PetDeathRecord::factory()->create(['cause' => 'accident']);

        $response = $this->put(route('pet-death-records.update', $record), [
            'pet_id' => $record->pet_id,
            'death_date' => $record->death_date->format('Y-m-d'),
            'cause' => 'illness',
        ]);

        $response->assertRedirect(route('pet-death-records.index'));
        $this->assertDatabaseHas('pet_death_records', [
            'id' => $record->id,
            'cause' => 'illness',
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $record = PetDeathRecord::factory()->create();

        $response = $this->delete(route('pet-death-records.destroy', $record));

        $response->assertRedirect(route('pet-death-records.index'));
        $this->assertDatabaseMissing('pet_death_records', ['id' => $record->id]);
    }
}
