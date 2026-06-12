<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\PetTutor;
use App\Models\Tutor;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class PetFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_can_create_pet_with_required_fields()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('pet-form')
            ->set('name', 'Rex')
            ->set('tutor_id', (string) $tutor->id)
            ->set('species', 'canine')
            ->set('gender', 'male')
            ->call('save')
            ->assertDispatched('pet-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('pets', ['name' => 'Rex', 'species' => 'canine', 'gender' => 'male']);
        $pet = Pet::where('name', 'Rex')->first();
        $this->assertDatabaseHas('pet_tutor', [
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'is_primary' => true,
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('pet-form')
            ->call('save')
            ->assertHasErrors(['name', 'tutor_id', 'species', 'gender']);
    }

    public function test_can_edit_existing_pet()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create(['name' => 'Nome Antigo']);
        PetTutor::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'is_primary' => true,
        ]);

        Livewire::test('pet-form', ['id' => $pet->id])
            ->assertSet('name', $pet->name)
            ->set('name', 'Nome Novo')
            ->call('save')
            ->assertDispatched('pet-saved');

        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Nome Novo']);
    }

    public function test_can_edit_pet_via_event()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create(['name' => 'Event Pet']);
        PetTutor::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'is_primary' => true,
        ]);

        Livewire::test('pet-form')
            ->dispatch('editPet', id: $pet->id)
            ->assertSet('petId', $pet->id)
            ->assertSet('name', 'Event Pet')
            ->set('name', 'Updated Event Pet')
            ->call('save')
            ->assertDispatched('pet-saved');

        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Updated Event Pet']);
    }

    public function test_can_create_pet_with_feline_species()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('pet-form')
            ->set('name', 'Mimi')
            ->set('tutor_id', (string) $tutor->id)
            ->set('species', 'feline')
            ->set('gender', 'female')
            ->call('save')
            ->assertDispatched('pet-saved');

        $this->assertDatabaseHas('pets', ['name' => 'Mimi', 'species' => 'feline']);
    }

    public function test_can_create_pet_via_create_for_tutor_event()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('pet-form')
            ->dispatch('createPetForTutor', tutorId: $tutor->id)
            ->assertSet('tutor_id', (string) $tutor->id)
            ->set('name', 'Tutor Event Pet')
            ->set('species', 'canine')
            ->set('gender', 'male')
            ->call('save')
            ->assertDispatched('pet-saved');

        $this->assertDatabaseHas('pets', ['name' => 'Tutor Event Pet']);
    }

    public function test_validates_invalid_species()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('pet-form')
            ->set('name', 'Alien')
            ->set('tutor_id', (string) $tutor->id)
            ->set('species', 'alien')
            ->set('gender', 'male')
            ->call('save')
            ->assertHasErrors(['species']);
    }
}
