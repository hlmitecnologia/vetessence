<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SignatureVerifyTest extends TestCase
{
    use DatabaseTransactions;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_prescription_is_signed_on_create()
    {
        $this->actingAsUser();

        $record = MedicalRecord::factory()->create();
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);

        $prescription->sign();

        $this->assertNotNull($prescription->fresh()->digital_signature);
        $this->assertNotNull($prescription->fresh()->signed_at);
        $this->assertNotNull($prescription->fresh()->content_hash);
        $this->assertTrue($prescription->fresh()->isSigned());
    }

    public function test_verification_endpoint_returns_valid_for_signed_prescription()
    {
        $this->actingAsUser();

        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);
        $prescription->sign();

        $response = $this->get(route('signature.verify', ['prescription', $prescription->id]));
        $response->assertOk();
        $response->assertSee('Assinatura Válida');
    }

    public function test_verification_endpoint_returns_invalid_for_unsigned_document()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);

        $response = $this->get(route('signature.verify', ['prescription', $prescription->id]));
        $response->assertOk();
        $response->assertSee('não assinado');
    }

    public function test_medical_record_is_signed_on_create()
    {
        $this->actingAsUser();

        $record = MedicalRecord::factory()->create();
        $record->sign();

        $this->assertNotNull($record->fresh()->digital_signature);
        $this->assertNotNull($record->fresh()->signed_at);
        $this->assertNotNull($record->fresh()->content_hash);
        $this->assertTrue($record->fresh()->isSigned());
    }

    public function test_verification_endpoint_returns_valid_for_signed_medical_record()
    {
        $this->actingAsUser();

        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $record->sign();

        $response = $this->get(route('signature.verify', ['medical-record', $record->id]));
        $response->assertOk();
        $response->assertSee('Assinatura Válida');
    }
}
