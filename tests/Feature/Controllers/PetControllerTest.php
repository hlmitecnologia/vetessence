<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Tutor;
use Database\Seeders\BreedDefaultSeeder;
use Tests\ModuleTestCase;

class PetControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->seed(BreedDefaultSeeder::class);
    }

    public function test_index()
    {
        Pet::factory()->count(3)->create();
        $response = $this->get(route('pets.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('pets.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->post(route('pets.store'), [
            'name' => 'Rex',
            'species' => 'canine',
            'gender' => 'male',
            'breed' => 'Labrador',
            'birth_date' => '2023-01-15',
            'weight' => 25.5,
            'color' => 'Caramelo',
            'tutor_id' => $tutor->id,
            'is_primary' => true,
        ]);
        $response->assertRedirect(route('pets.index'));
        $this->assertDatabaseHas('pets', ['name' => 'Rex', 'species' => 'canine']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('pets.store'), ['name' => '']);
        $response->assertSessionHasErrors(['name', 'species', 'gender', 'tutor_id']);
    }

    public function test_show()
    {
        $pet = Pet::factory()->create();
        $response = $this->get(route('pets.show', $pet));
        $response->assertOk();
    }

    public function test_edit()
    {
        $pet = Pet::factory()->create();
        $response = $this->get(route('pets.edit', $pet));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $pet = Pet::factory()->create(['name' => 'Bolinha']);
        $response = $this->put(route('pets.update', $pet), [
            'name' => 'Bolinha Atualizado',
            'species' => 'canine',
            'gender' => 'male',
        ]);
        $response->assertRedirect(route('pets.index'));
        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Bolinha Atualizado']);
    }

    public function test_destroy_deletes_record()
    {
        $pet = Pet::factory()->create();
        $response = $this->delete(route('pets.destroy', $pet));
        $response->assertRedirect(route('pets.index'));
        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }
}
