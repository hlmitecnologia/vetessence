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
        $this->actingAsUser();
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
        $this->actingAsUser();
        $response = $this->get(route('prescriptions.verify', 'invalid-hash-123'));
        $response->assertOk();
        $response->assertSee('não encontrada');
    }
}
