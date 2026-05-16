<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use Tests\ModuleTestCase;

class PatientTimelineTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_timeline_page_loads()
    {
        $pet = Pet::factory()->create();
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
    }

    public function test_timeline_shows_medical_records()
    {
        $pet = Pet::factory()->create();
        $mr = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Prontuário');
    }

    public function test_timeline_shows_vaccinations()
    {
        $pet = Pet::factory()->create();
        $vax = Vaccination::factory()->create(['pet_id' => $pet->id]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Vacina');
    }

    public function test_timeline_empty_state()
    {
        $pet = Pet::factory()->create();
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Nenhum evento');
    }
}
