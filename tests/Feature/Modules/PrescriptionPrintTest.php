<?php

namespace Tests\Feature\Modules;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class PrescriptionPrintTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_print_page()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = User::factory()->create();
        $mr = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consulta',
            'description' => 'Consulta de teste',
        ]);
        $prescription = Prescription::create([
            'medical_record_id' => $mr->id,
            'medication' => 'Medicamento Teste',
            'dosage' => '10mg',
            'frequency' => '8/8h',
            'duration' => '7 dias',
            'created_by' => $vet->id,
        ]);

        $response = $this->get(route('prescriptions.print', $prescription));
        $response->assertOk();
    }
}
