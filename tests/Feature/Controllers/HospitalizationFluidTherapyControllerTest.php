<?php

namespace Tests\Feature\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationFluidTherapy;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class HospitalizationFluidTherapyControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_store_creates_fluid_therapy()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalizations.fluid-therapies.store', $hospitalization), [
            'fluid_type' => 'Ringer Lactato',
            'rate' => 10.5,
            'volume' => 500,
            'start_time' => now()->format('Y-m-d H:i:s'),
            'route' => 'IV',
            'observations' => 'Fluidoterapia de manutenção',
        ]);

        $response->assertRedirect(route('hospitalizations.show', $hospitalization));
        $this->assertDatabaseHas('hospitalization_fluid_therapy', [
            'hospitalization_id' => $hospitalization->id,
            'fluid_type' => 'Ringer Lactato',
            'rate' => 10.5,
            'volume' => 500,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalizations.fluid-therapies.store', $hospitalization), []);
        $response->assertSessionHasErrors(['fluid_type']);
    }

    public function test_store_validates_rate_is_non_negative()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalizations.fluid-therapies.store', $hospitalization), [
            'fluid_type' => 'SF 0,9%',
            'rate' => -1,
        ]);
        $response->assertSessionHasErrors(['rate']);
    }

    public function test_destroy_deletes_fluid_therapy()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);
        $therapy = HospitalizationFluidTherapy::create([
            'hospitalization_id' => $hospitalization->id,
            'fluid_type' => 'Ringer Lactato',
        ]);

        $response = $this->delete(route('hospitalizations.fluid-therapies.destroy', [
            $hospitalization,
            $therapy,
        ]));

        $response->assertRedirect(route('hospitalizations.show', $hospitalization));
        $this->assertDatabaseMissing('hospitalization_fluid_therapy', ['id' => $therapy->id]);
    }

    public function test_destroy_returns_success_message()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);
        $therapy = HospitalizationFluidTherapy::create([
            'hospitalization_id' => $hospitalization->id,
            'fluid_type' => 'Ringer Lactato',
        ]);

        $response = $this->delete(route('hospitalizations.fluid-therapies.destroy', [
            $hospitalization,
            $therapy,
        ]));

        $response->assertSessionHas('success');
    }

    public function test_store_with_end_time_after_start_time()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalizations.fluid-therapies.store', $hospitalization), [
            'fluid_type' => 'Ringer Lactato',
            'start_time' => now()->format('Y-m-d H:i:s'),
            'end_time' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);
        $response->assertSessionHasErrors(['end_time']);
    }
}
