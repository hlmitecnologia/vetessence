<?php

namespace Tests\Feature\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use Tests\ModuleTestCase;

class PublicPrescriptionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

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

    public function test_does_not_update_verified_at_if_already_verified()
    {
        $prescription = $this->createPrescription();
        $prescription->update(['verified_at' => now()->subDay()]);
        $original = $prescription->fresh()->verified_at;

        $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $this->assertEquals(
            $original->format('Y-m-d H:i:s'),
            $prescription->fresh()->verified_at->format('Y-m-d H:i:s')
        );
    }

    public function test_authenticated_user_sees_admin_verify_view()
    {
        $prescription = $this->createPrescription();
        $this->loginAs('veterinario');

        $response = $this->get(route('prescriptions.verify', $prescription->verification_hash));

        $response->assertOk();
        $response->assertSee('Prescrição válida e verificada');
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
