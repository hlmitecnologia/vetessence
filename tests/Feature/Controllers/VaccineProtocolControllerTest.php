<?php

namespace Tests\Feature\Controllers;

use App\Models\VaccineProtocol;
use Tests\ModuleTestCase;

class VaccineProtocolControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        VaccineProtocol::factory()->count(3)->create();
        $response = $this->get(route('vaccine-protocols.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('vaccine-protocols.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('vaccine-protocols.store'), [
            'species' => 'canine',
            'vaccine_name' => 'V8',
            'age_start_weeks' => 6,
            'age_end_weeks' => 8,
            'is_initial' => true,
            'dose_number' => 1,
            'booster_interval_months' => 12,
            'is_core' => true,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('vaccine-protocols.index'));
        $this->assertDatabaseHas('vaccine_protocols', ['vaccine_name' => 'V8', 'species' => 'canine']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('vaccine-protocols.store'), []);
        $response->assertSessionHasErrors(['species', 'vaccine_name']);
    }

    public function test_show()
    {
        $protocol = VaccineProtocol::factory()->create();
        $response = $this->get(route('vaccine-protocols.show', $protocol));
        $response->assertOk();
    }

    public function test_edit()
    {
        $protocol = VaccineProtocol::factory()->create();
        $response = $this->get(route('vaccine-protocols.edit', $protocol));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $protocol = VaccineProtocol::factory()->create();
        $response = $this->put(route('vaccine-protocols.update', $protocol), [
            'species' => 'feline',
            'vaccine_name' => 'V4',
            'age_start_weeks' => 8,
            'age_end_weeks' => 10,
            'is_initial' => true,
            'dose_number' => 1,
            'booster_interval_months' => 12,
            'is_core' => true,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('vaccine-protocols.index'));
        $this->assertDatabaseHas('vaccine_protocols', ['id' => $protocol->id, 'vaccine_name' => 'V4']);
    }

    public function test_destroy_deletes_record()
    {
        $protocol = VaccineProtocol::factory()->create();
        $response = $this->delete(route('vaccine-protocols.destroy', $protocol));
        $response->assertRedirect(route('vaccine-protocols.index'));
        $this->assertDatabaseMissing('vaccine_protocols', ['id' => $protocol->id]);
    }
}
