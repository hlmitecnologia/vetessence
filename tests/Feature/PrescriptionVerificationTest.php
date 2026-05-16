<?php

namespace Tests\Feature;

use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PrescriptionVerificationTest extends TestCase
{
    use DatabaseTransactions;

    private function actingAsUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_hash_generated_on_create()
    {
        $record = MedicalRecord::factory()->create();
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);

        $this->assertNotNull($prescription->verification_hash);
        $this->assertEquals(64, strlen($prescription->verification_hash));
    }

    public function test_verification_route_returns_valid()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
        ]);

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));
        $response->assertOk();
        $response->assertSee('válida');
    }

    public function test_verification_route_invalid_hash()
    {
        $response = $this->get(route('prescriptions.verify', 'invalid-hash-123'));
        $response->assertOk();
        $response->assertSee('não encontrada');
    }

    public function test_public_access_no_auth_required()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
        ]);

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));
        $response->assertOk();
        $response->assertSee('válida');
    }

    public function test_verified_at_set_on_first_verification()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $prescription = Prescription::factory()->create([
            'medical_record_id' => $record->id,
        ]);

        $this->assertNull($prescription->fresh()->verified_at);
        $this->get(route('prescriptions.verify', $prescription->verification_hash));
        $this->assertNotNull($prescription->fresh()->verified_at);
    }

    public function test_verification_hash_booted_on_create()
    {
        $record = MedicalRecord::factory()->create();
        $prescription = new Prescription([
            'medical_record_id' => $record->id,
            'medication' => 'Teste',
            'dosage' => '10mg',
        ]);
        $prescription->save();

        $this->assertNotNull($prescription->verification_hash);
        $this->assertEquals(64, strlen($prescription->verification_hash));
    }
}
