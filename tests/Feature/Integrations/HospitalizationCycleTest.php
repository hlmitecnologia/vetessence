<?php

namespace Tests\Feature\Integrations;

use App\Models\Branch;
use App\Models\Hospitalization;
use App\Models\HospitalizationDailyRecord;
use App\Models\HospitalizationFluidTherapy;
use App\Models\HospitalizationPrescription;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class HospitalizationCycleTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->vet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_full_hospitalization_lifecycle()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $admitResponse = $this->post(route('hospitalizations.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $this->vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Pós-operatório de fratura',
            'initial_diagnosis' => 'Fratura de fêmur',
            'department' => 'Ortopedia',
            'bed' => 'A-101',
            'is_emergency' => false,
        ]);
        $admitResponse->assertSessionDoesntHaveErrors();
        $admitResponse->assertRedirect();

        $this->assertDatabaseHas('hospitalizations', [
            'pet_id' => $pet->id,
            'status' => 'active',
            'department' => 'Ortopedia',
        ]);
        $hospitalization = Hospitalization::where('pet_id', $pet->id)->first();

        $dailyRecordResponse = $this->post(
            route('hospitalizations.daily-records.store', $hospitalization),
            [
                'hospitalization_id' => $hospitalization->id,
                'user_id' => $this->vet->id,
                'record_date' => now()->format('Y-m-d'),
                'shift' => 'morning',
                'subjective' => 'Paciente alerta',
                'objective' => 'Ferida operatória limpa',
                'assessment' => 'Evoluindo bem',
                'plan' => 'Manter medicação',
                'temperature' => 38.5,
                'heart_rate' => 90,
                'respiratory_rate' => 20,
                'appetite' => 'normal',
                'hydration' => 'good',
                'medications_given' => 'Analgésico administrado',
            ]
        );
        $dailyRecordResponse->assertSessionDoesntHaveErrors();
        $dailyRecordResponse->assertRedirect();

        $this->assertDatabaseHas('hospitalization_daily_records', [
            'hospitalization_id' => $hospitalization->id,
            'shift' => 'morning',
            'temperature' => 38.5,
        ]);

        $fluidResponse = $this->post(
            route('hospitalizations.fluid-therapies.store', $hospitalization),
            [
                'hospitalization_id' => $hospitalization->id,
                'fluid_type' => 'Ringer Lactato',
                'rate' => 10,
                'volume' => 500,
                'start_time' => now()->format('Y-m-d\TH:i'),
                'route' => 'IV',
                'observations' => 'Fluidoterapia de manutenção',
                'branch_id' => $this->branch->id,
            ]
        );
        $fluidResponse->assertSessionDoesntHaveErrors();
        $fluidResponse->assertRedirect();

        $this->assertDatabaseHas('hospitalization_fluid_therapy', [
            'hospitalization_id' => $hospitalization->id,
            'fluid_type' => 'Ringer Lactato',
        ]);

        $prescriptionResponse = $this->post(
            route('hospitalizations.prescriptions.store', $hospitalization),
            [
                'hospitalization_id' => $hospitalization->id,
                'medication' => 'Cetoprofeno',
                'dosage' => '2',
                'unit' => 'mg/kg',
                'frequency' => 'SID',
                'route' => 'IV',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(3)->format('Y-m-d'),
                'status' => 'active',
                'prescribed_by' => $this->vet->id,
                'notes' => 'Analgésico',
                'branch_id' => $this->branch->id,
            ]
        );
        $prescriptionResponse->assertSessionDoesntHaveErrors();
        $prescriptionResponse->assertRedirect();

        $this->assertDatabaseHas('hospitalization_prescriptions', [
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Cetoprofeno',
            'status' => 'active',
        ]);

        $dischargeResponse = $this->put(route('hospitalizations.update', $hospitalization), [
            'bed' => 'A-101',
            'status' => 'discharged',
            'discharged_at' => now()->format('Y-m-d'),
            'discharge_summary' => 'Paciente recuperado, alta concedida',
            'discharge_instructions' => 'Repouso por 7 dias',
        ]);
        $dischargeResponse->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('hospitalizations', [
            'id' => $hospitalization->id,
            'status' => 'discharged',
            'discharge_summary' => 'Paciente recuperado, alta concedida',
        ]);
        $this->assertNotNull($hospitalization->fresh()->discharged_at);
    }
}
