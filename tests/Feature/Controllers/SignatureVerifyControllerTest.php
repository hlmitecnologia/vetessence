<?php

namespace Tests\Feature\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use Tests\ModuleTestCase;

class SignatureVerifyControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_verify_prescription_without_signature_shows_not_signed()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
        ]);

        $response = $this->get(route('signature.verify', ['prescription', $prescription->id]));

        $response->assertOk();
        $response->assertSee('Documento não assinado digitalmente');
    }

    public function test_verify_medical_record_without_signature_shows_not_signed()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->get(route('signature.verify', ['medical-record', $record->id]));

        $response->assertOk();
        $response->assertSee('Documento não assinado digitalmente');
    }

    public function test_verify_with_unknown_model_returns_404()
    {
        $response = $this->get(route('signature.verify', ['invalid-model', 1]));

        $response->assertNotFound();
    }

    public function test_verify_with_nonexistent_record_returns_404()
    {
        $response = $this->get(route('signature.verify', ['prescription', 99999]));

        $response->assertNotFound();
    }

    public function test_verify_signed_prescription_returns_valid()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'signed_at' => now(),
        ]);

        $response = $this->get(route('signature.verify', ['prescription', $prescription->id]));

        $response->assertOk();
    }

    public function test_verify_signed_medical_record_returns_valid()
    {
        $record = MedicalRecord::factory()->create([
            'signed_at' => now(),
        ]);

        $response = $this->get(route('signature.verify', ['medical-record', $record->id]));

        $response->assertOk();
    }

    public function test_verify_signed_prescription_returns_ok()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'signed_at' => now(),
        ]);

        $response = $this->get(route('signature.verify', ['prescription', $prescription->id]));

        $response->assertOk();
    }
}
