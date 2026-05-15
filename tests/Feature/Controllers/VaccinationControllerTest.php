<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Vaccination;
use Tests\ModuleTestCase;

class VaccinationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Vaccination::factory()->count(3)->create();
        $response = $this->get(route('vaccinations.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('vaccinations.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('vaccinations.store'), [
            'pet_id' => $pet->id,
            'vaccine' => 'V8',
            'date' => now()->format('Y-m-d'),
            'vet_id' => $vet->id,
            'batch' => 'LOTE001',
            'next_date' => now()->addYear()->format('Y-m-d'),
            'notes' => 'Sem reações adversas',
        ]);
        $response->assertRedirect(route('vaccinations.index'));
        $this->assertDatabaseHas('vaccinations', ['pet_id' => $pet->id, 'vaccine' => 'V8']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('vaccinations.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'vaccine', 'date', 'vet_id']);
    }

    public function test_show()
    {
        $vaccination = Vaccination::factory()->create();
        $response = $this->get(route('vaccinations.show', $vaccination));
        $response->assertOk();
    }

    public function test_edit()
    {
        $vaccination = Vaccination::factory()->create();
        $response = $this->get(route('vaccinations.edit', $vaccination));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $vet = User::factory()->create();
        $vaccination = Vaccination::factory()->create();

        $response = $this->put(route('vaccinations.update', $vaccination), [
            'vaccine' => 'V10',
            'date' => now()->format('Y-m-d'),
            'vet_id' => $vet->id,
            'batch' => 'LOTE002',
        ]);
        $response->assertRedirect(route('vaccinations.index'));
        $this->assertDatabaseHas('vaccinations', ['id' => $vaccination->id, 'vaccine' => 'V10']);
    }

    public function test_destroy_deletes_record()
    {
        $vaccination = Vaccination::factory()->create();
        $response = $this->delete(route('vaccinations.destroy', $vaccination));
        $response->assertRedirect(route('vaccinations.index'));
        $this->assertDatabaseMissing('vaccinations', ['id' => $vaccination->id]);
    }
}
