<?php

namespace Tests\Feature\Controllers;

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class HospitalizationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('hospitalizations.index'));
        $response->assertOk();
    }

    public function test_store_creates_hospitalization()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->post(route('hospitalizations.store'), [
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Pós-operatório',
            'initial_diagnosis' => 'Fratura',
            'department' => 'Cirurgia',
            'is_emergency' => false,
            'status' => 'admitted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalizations', [
            'pet_id' => $pet->id,
            'admission_reason' => 'Pós-operatório',
        ]);
    }

    public function test_show()
    {
        $hospitalization = Hospitalization::factory()->create();

        $response = $this->get(route('hospitalizations.show', $hospitalization));
        $response->assertOk();
    }

    public function test_update()
    {
        $hospitalization = Hospitalization::factory()->create();

        $response = $this->put(route('hospitalizations.update', $hospitalization), [
            'bed' => 'A-101',
            'status' => 'admitted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalizations', [
            'id' => $hospitalization->id,
            'bed' => 'A-101',
        ]);
    }

    public function test_destroy()
    {
        $hospitalization = Hospitalization::factory()->create(['status' => 'discharged']);

        $response = $this->delete(route('hospitalizations.destroy', $hospitalization));
        $response->assertRedirect();
        $this->assertDatabaseMissing('hospitalizations', ['id' => $hospitalization->id]);
    }
}
