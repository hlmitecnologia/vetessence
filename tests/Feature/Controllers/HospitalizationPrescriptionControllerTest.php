<?php

namespace Tests\Feature\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationPrescription;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class HospitalizationPrescriptionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_store_creates_prescription()
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
            'admission_reason' => 'Tratamento',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalizations.prescriptions.store', $hospitalization), [
            'medication' => 'Amoxicilina',
            'dosage' => '500mg',
            'frequency' => '8/8h',
            'route' => 'oral',
            'notes' => 'Por 7 dias',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalization_prescriptions', [
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Amoxicilina',
        ]);
    }

    public function test_update()
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
            'admission_reason' => 'Tratamento',
            'status' => 'admitted',
        ]);

        $prescription = HospitalizationPrescription::create([
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Amoxicilina',
            'dosage' => '500mg',
            'frequency' => '8/8h',
            'prescribed_by' => $vet->id,
        ]);

        $response = $this->put(
            route('hospitalizations.prescriptions.update', [$hospitalization, $prescription]),
            [
                'medication' => 'Amoxicilina',
                'dosage' => '1g',
                'frequency' => '12/12h',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalization_prescriptions', [
            'id' => $prescription->id,
            'dosage' => '1g',
        ]);
    }

    public function test_destroy()
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
            'admission_reason' => 'Tratamento',
            'status' => 'admitted',
        ]);

        $prescription = HospitalizationPrescription::create([
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Amoxicilina',
            'dosage' => '500mg',
            'frequency' => '8/8h',
            'prescribed_by' => $vet->id,
        ]);

        $response = $this->delete(
            route('hospitalizations.prescriptions.destroy', [$hospitalization, $prescription])
        );
        $response->assertRedirect();
        $this->assertDatabaseMissing('hospitalization_prescriptions', ['id' => $prescription->id]);
    }
}
