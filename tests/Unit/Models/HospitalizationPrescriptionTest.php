<?php

namespace Tests\Unit\Models;

use App\Models\HospitalizationPrescription;
use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HospitalizationPrescriptionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $vet = User::factory()->create();
        HospitalizationPrescription::create([
            'hospitalization_id' => $h->id, 'medication' => 'Metronidazol',
            'dosage' => '10mg', 'unit' => 'mg/kg', 'frequency' => 'BID',
            'route' => 'VO', 'status' => 'active', 'prescribed_by' => $vet->id,
        ]);
        $this->assertDatabaseHas('hospitalization_prescriptions', ['hospitalization_id' => $h->id, 'medication' => 'Metronidazol']);
    }

    public function test_hospitalization_relationship()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $hp = HospitalizationPrescription::create(['hospitalization_id' => $h->id, 'medication' => 'Teste', 'dosage' => '1mg', 'status' => 'active']);
        $this->assertInstanceOf(Hospitalization::class, $hp->hospitalization);
    }

    public function test_prescribed_by_relationship()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $vet = User::factory()->create();
        $hp = HospitalizationPrescription::create(['hospitalization_id' => $h->id, 'medication' => 'Teste', 'dosage' => '1mg', 'status' => 'active', 'prescribed_by' => $vet->id]);
        $this->assertInstanceOf(User::class, $hp->prescribedBy);
    }
}
