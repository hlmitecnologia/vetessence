<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PrescriptionVerificationTest extends TestCase
{
    use DatabaseTransactions;

    private function createPrescription(): Prescription
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);
        return Prescription::factory()->create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicilina',
            'dosage' => '500mg',
        ]);
    }

    public function test_public_access_to_valid_hash_returns_200()
    {
        $prescription = $this->createPrescription();

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $response->assertOk();
        $response->assertSee('Prescrição válida e verificada');
        $response->assertSee('Amoxicilina');
    }

    public function test_public_access_to_invalid_hash_returns_200_with_error()
    {
        $response = $this->get(route('prescriptions.verify', 'invalidhash123'));

        $response->assertOk();
        $response->assertSee('não encontrada');
    }

    public function test_public_access_sets_verified_at()
    {
        $prescription = $this->createPrescription();
        $this->assertNull($prescription->fresh()->verified_at);

        $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $this->assertNotNull($prescription->fresh()->verified_at);
    }

    public function test_rate_limit_after_too_many_requests()
    {
        $prescription = $this->createPrescription();
        $hash = $prescription->verification_hash;

        for ($i = 0; $i < 10; $i++) {
            $this->get(route('prescriptions.verify', $hash));
        }

        $response = $this->get(route('prescriptions.verify', $hash));
        $response->assertStatus(429);
    }

    public function test_authenticated_user_sees_admin_verify_view()
    {
        $prescription = $this->createPrescription();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $response->assertOk();
        $response->assertSee('Prescrição válida e verificada');
    }

    public function test_prescription_model_verify_url_accessor()
    {
        $prescription = $this->createPrescription();

        $expected = url("/r/{$prescription->verification_hash}");
        $this->assertEquals($expected, $prescription->verify_url);
    }

    public function test_public_view_shows_prescription_details()
    {
        $prescription = $this->createPrescription();

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $response->assertOk();
        $response->assertSee($prescription->medication);
        $response->assertSee($prescription->dosage);
    }
}
