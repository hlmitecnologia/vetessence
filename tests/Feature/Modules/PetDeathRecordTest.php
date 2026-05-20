<?php

namespace Tests\Feature\Modules;

use App\Models\Pet;
use App\Models\PetDeathRecord;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class PetDeathRecordTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('pet-death-records.index'));
        $response->assertOk();
    }

    public function test_create_creates_record()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->post(route('pet-death-records.store'), [
            'pet_id' => $pet->id,
            'death_date' => now()->format('Y-m-d'),
            'cause' => 'natural causes',
        ]);
        $response->assertRedirect(route('pet-death-records.index'));
        $this->assertDatabaseHas('pet_death_records', ['pet_id' => $pet->id, 'cause' => 'natural causes']);
    }

    public function test_create_validates_required_fields()
    {
        $response = $this->post(route('pet-death-records.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'death_date']);
    }

    public function test_show()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $record = PetDeathRecord::factory()->create([
            'pet_id' => $pet->id,
            'registered_by' => auth()->id(),
        ]);

        $response = $this->get(route('pet-death-records.show', $record));
        $response->assertOk();
    }

    public function test_destroy_removes_record()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $record = PetDeathRecord::factory()->create([
            'pet_id' => $pet->id,
            'registered_by' => auth()->id(),
        ]);

        $response = $this->delete(route('pet-death-records.destroy', $record));
        $response->assertRedirect(route('pet-death-records.index'));
        $this->assertDatabaseMissing('pet_death_records', ['id' => $record->id]);
    }
}
