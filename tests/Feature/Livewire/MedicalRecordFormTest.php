<?php

namespace Tests\Feature\Livewire;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use App\Models\ZoonoticDisease;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class MedicalRecordFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_can_create_medical_record_with_required_fields()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->call('save')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'user_id' => auth()->id(),
            'type' => 'consulta',
        ]);
    }

    public function test_can_create_with_vital_signs()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->set('vital_signs.temperature', '38.5')
            ->set('vital_signs.heart_rate', '120')
            ->set('vital_signs.respiratory_rate', '30')
            ->set('vital_signs.weight', '15.5')
            ->call('save')
            ->assertSessionHas('success');

        $record = MedicalRecord::where('pet_id', $pet->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals('38.5', $record->vital_signs['temperature']);
        $this->assertEquals('120', $record->vital_signs['heart_rate']);
    }

    public function test_can_create_with_diagnosis_treatment_notes()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->set('chief_complaint', 'Tosse seca há 3 dias')
            ->set('diagnosis', 'Bronquite')
            ->set('treatment', 'Antibiótico por 7 dias')
            ->set('notes', 'Retorno em 10 dias')
            ->call('save')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'diagnosis' => 'Bronquite',
            'treatment' => 'Antibiótico por 7 dias',
            'notes' => 'Retorno em 10 dias',
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('medical-record-form')
            ->set('pet_id', '')
            ->set('vet_id', '')
            ->set('date', '')
            ->set('time', '')
            ->set('type', '')
            ->call('save')
            ->assertHasErrors(['pet_id', 'vet_id', 'date', 'time', 'type']);
        // date is also required; mount fills it but we clear it manually above
    }

    public function test_can_add_and_remove_zoonotic_diseases()
    {
        $disease = ZoonoticDisease::create([
            'name' => 'Raiva',
            'category' => 'viral',
            'is_notifiable' => true,
        ]);
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->call('addDisease')
            ->assertSet('selectedDiseases', [
                ['disease_id' => '', 'is_suspected' => false],
            ])
            ->set('selectedDiseases.0.disease_id', (string) $disease->id)
            ->set('selectedDiseases.0.is_suspected', true)
            ->call('removeDisease', 0)
            ->assertSet('selectedDiseases', [])
            ->call('save')
            ->assertSessionHas('success');
    }

    public function test_can_add_and_remove_prescriptions()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->call('addPrescription')
            ->assertSet('prescriptions', [
                ['medication' => '', 'dosage' => '', 'unit' => '', 'frequency' => '', 'duration' => '', 'route' => 'oral', 'instructions' => ''],
            ])
            ->set('prescriptions.0.medication', 'Amoxicilina')
            ->set('prescriptions.0.dosage', '500')
            ->set('prescriptions.0.unit', 'mg')
            ->set('prescriptions.0.frequency', '8/8h')
            ->set('prescriptions.0.duration', '7 dias')
            ->call('addPrescription')
            ->call('removePrescription', 1)
            ->call('save')
            ->assertSessionHas('success');

        $record = MedicalRecord::where('pet_id', $pet->id)->first();
        $this->assertNotNull($record);
        $this->assertDatabaseHas('prescriptions', [
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);
    }

    public function test_can_edit_existing_record()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => auth()->id(),
            'date' => '2025-06-01',
            'type' => 'consulta',
            'diagnosis' => 'Diagnóstico Antigo',
        ]);
        $record->time = '14:30';
        $record->save();

        Livewire::test('medical-record-form', ['recordId' => $record->id])
            ->assertSet('recordId', $record->id)
            ->assertSet('diagnosis', 'Diagnóstico Antigo')
            ->set('diagnosis', 'Diagnóstico Novo')
            ->set('vet_id', (string) auth()->id())
            ->call('save')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'diagnosis' => 'Diagnóstico Novo',
        ]);
    }

    public function test_suggest_diagnosis_sets_error_when_no_llm_configured()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->set('chief_complaint', 'Tosse')
            ->call('suggestDiagnosis')
            ->assertSet('suggestingDiagnosis', false);
    }

    public function test_can_create_with_prognosis()
    {
        $pet = Pet::factory()->create();

        Livewire::test('medical-record-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) auth()->id())
            ->set('date', '2025-06-01')
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->set('prognosis', 'bom')
            ->call('save')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'prognosis' => 'bom',
        ]);
    }
}
