<?php

namespace Tests\Feature\Modules;

use App\Models\Pet;
use App\Models\VaccineProtocol;
use Tests\ModuleTestCase;

class VaccinationProtocolTest extends ModuleTestCase
{
    public function test_index_requires_authentication()
    {
        $response = $this->get(route('vaccine-protocols.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_as_veterinario()
    {
        $this->loginAs('veterinario');
        VaccineProtocol::factory()->create(['species' => 'canine', 'vaccine_name' => 'V10']);
        $response = $this->get(route('vaccine-protocols.index'));
        $response->assertOk();
        $response->assertSee('V10');
    }

    public function test_store_creates_protocol()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('vaccine-protocols.store'), [
            'species' => 'feline', 'vaccine_name' => 'V3',
            'age_start_weeks' => 8, 'age_end_weeks' => 12,
            'is_initial' => true, 'dose_number' => 1,
            'booster_interval_months' => 12, 'is_core' => true, 'is_active' => true,
        ]);
        $response->assertRedirect(route('vaccine-protocols.index'));
        $this->assertDatabaseHas('vaccine_protocols', ['vaccine_name' => 'V3', 'species' => 'feline']);
    }

    public function test_store_validates_required_fields()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('vaccine-protocols.store'), []);
        $response->assertSessionHasErrors(['species', 'vaccine_name']);
    }

    public function test_suggest_for_pet_scope()
    {
        $this->loginAs('veterinario');
        VaccineProtocol::factory()->create(['species' => 'canine', 'age_start_weeks' => 6, 'age_end_weeks' => 52, 'is_active' => true]);
        $pet = Pet::factory()->create(['species' => 'canine', 'birth_date' => now()->subMonths(3)]);
        $protocols = VaccineProtocol::suggestForPet($pet);
        $this->assertTrue($protocols->isNotEmpty());
    }

    public function test_index_filters_by_species()
    {
        $this->loginAs('veterinario');
        VaccineProtocol::factory()->create(['species' => 'canine', 'vaccine_name' => 'V8']);
        VaccineProtocol::factory()->create(['species' => 'feline', 'vaccine_name' => 'V4']);
        $response = $this->get(route('vaccine-protocols.index', ['species' => 'canine']));
        $response->assertOk()->assertSee('V8')->assertDontSee('V4');
    }

    public function test_destroy_removes_protocol()
    {
        $this->loginAs('veterinario');
        $protocol = VaccineProtocol::factory()->create();
        $response = $this->delete(route('vaccine-protocols.destroy', $protocol));
        $response->assertRedirect(route('vaccine-protocols.index'));
        $this->assertDatabaseMissing('vaccine_protocols', ['id' => $protocol->id]);
    }

    public function test_gate_blocks_recepcionista()
    {
        $this->loginAs('recepcionista');
        $response = $this->get(route('vaccine-protocols.index'));
        $response->assertForbidden();
    }
}
