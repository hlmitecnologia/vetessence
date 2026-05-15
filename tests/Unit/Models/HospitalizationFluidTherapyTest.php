<?php

namespace Tests\Unit\Models;

use App\Models\HospitalizationFluidTherapy;
use App\Models\Hospitalization;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HospitalizationFluidTherapyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        HospitalizationFluidTherapy::create([
            'hospitalization_id' => $h->id, 'fluid_type' => 'ringer',
            'rate' => '20ml/h', 'volume' => '500ml', 'route' => 'iv',
        ]);
        $this->assertDatabaseHas('hospitalization_fluid_therapy', ['hospitalization_id' => $h->id, 'fluid_type' => 'ringer']);
    }

    public function test_hospitalization_relationship()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $ft = HospitalizationFluidTherapy::create(['hospitalization_id' => $h->id, 'fluid_type' => 'ringer']);
        $this->assertInstanceOf(Hospitalization::class, $ft->hospitalization);
    }
}
