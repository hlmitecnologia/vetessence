<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Services\Llm\LlmProvider;
use App\Services\Llm\LlmResult;
use App\Services\Llm\LlmService;
use Tests\ModuleTestCase;

class LlmServiceTest extends ModuleTestCase
{
    public function test_suggest_diagnosis_with_mock_provider_success()
    {
        LlmConfig::factory()->create(['is_active' => true]);
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'chief_complaint' => 'Tosse',
            'treatment' => 'Antibiótico',
        ]);

        $mock = $this->createMock(LlmProvider::class);
        $mock->method('generate')->willReturn(
            LlmResult::success(content: 'Diagnóstico: Bronquite')
        );

        $service = new LlmService($mock);
        $result = $service->suggestDiagnosis($record);

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Bronquite', $result->content);
    }

    public function test_suggest_diagnosis_passes_treatment_in_prompt()
    {
        LlmConfig::factory()->create(['is_active' => true]);
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'treatment' => 'cefalexina 30mg/kg BID por 10 dias',
        ]);

        $mock = $this->createMock(LlmProvider::class);
        $mock->expects($this->once())
            ->method('generate')
            ->with($this->anything(), $this->stringContains('cefalexina'))
            ->willReturn(LlmResult::success(content: 'ok'));

        $service = new LlmService($mock);
        $service->suggestDiagnosis($record);
    }

    public function test_suggest_diagnosis_with_prescriptions()
    {
        LlmConfig::factory()->create(['is_active' => true]);
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Doxiciclina',
            'dosage' => '10',
            'unit' => 'mg/kg',
            'frequency' => 'BID',
        ]);

        $mock = $this->createMock(LlmProvider::class);
        $mock->expects($this->once())
            ->method('generate')
            ->with($this->anything(), $this->stringContains('Doxiciclina'))
            ->willReturn(LlmResult::success(content: 'ok'));

        $service = new LlmService($mock);
        $service->suggestDiagnosis($record);
    }

    public function test_suggest_diagnosis_create_mode_no_pet_relationship()
    {
        LlmConfig::factory()->create(['is_active' => true]);
        $pet = Pet::factory()->create();

        $record = new MedicalRecord();
        $record->pet_id = $pet->id;
        $record->chief_complaint = 'Vômito';

        $mock = $this->createMock(LlmProvider::class);
        $mock->method('generate')->willReturn(LlmResult::success(content: 'ok'));

        $service = new LlmService($mock);
        $result = $service->suggestDiagnosis($record);

        $this->assertTrue($result->success);
    }
}
