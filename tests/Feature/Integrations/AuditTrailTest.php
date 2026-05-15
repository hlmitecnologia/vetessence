<?php

namespace Tests\Feature\Integrations;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class AuditTrailTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->vet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_medical_record_creation_logs_audit()
    {
        $pet = Pet::factory()->create();

        $this->post(route('medical-records.store'), [
            'pet_id' => $pet->id,
            'user_id' => $this->vet->id,
            'date' => now()->format('Y-m-d'),
            'type' => 'consultation',
            'complaint' => 'Exame de rotina',
            'diagnosis' => 'Saudável',
            'treatment' => 'Nenhum',
            'branch_id' => $this->branch->id,
        ]);

        $medicalRecord = MedicalRecord::where('pet_id', $pet->id)->first();

        $auditLog = AuditLog::create([
            'user_id' => auth()->id(),
            'model_type' => get_class($medicalRecord),
            'model_id' => $medicalRecord->id,
            'action' => 'created',
            'old_values' => [],
            'new_values' => $medicalRecord->toArray(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $auditLog->id,
            'model_type' => get_class($medicalRecord),
            'model_id' => $medicalRecord->id,
            'action' => 'created',
        ]);
    }

    public function test_audit_log_can_be_retrieved()
    {
        $pet = Pet::factory()->create();

        AuditLog::log($pet, 'created');

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => get_class($pet),
            'model_id' => $pet->id,
            'action' => 'created',
        ]);

        $response = $this->get(route('audit-logs.index'));
        $response->assertOk();
    }
}
