<?php

namespace Tests\Unit\Models;

use App\Models\AnesthesiaVitalSign;
use App\Models\AnesthesiaMonitoring;
use App\Models\Pet;
use App\Models\User;
use App\Models\Surgery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnesthesiaVitalSignTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'scheduled_date' => now(), 'surgery_type' => 'castracao', 'status' => 'scheduled']);
        $monitoring = AnesthesiaMonitoring::create(['surgery_id' => $surgery->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id]);
        AnesthesiaVitalSign::create([
            'anesthesia_monitoring_id' => $monitoring->id, 'recorded_at' => now(),
            'heart_rate' => 120, 'respiratory_rate' => 30,
        ]);
        $this->assertDatabaseHas('anesthesia_vital_signs', ['anesthesia_monitoring_id' => $monitoring->id, 'heart_rate' => 120]);
    }

    public function test_anesthesia_monitoring_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'scheduled_date' => now(), 'surgery_type' => 'castracao', 'status' => 'scheduled']);
        $monitoring = AnesthesiaMonitoring::create(['surgery_id' => $surgery->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id]);
        $vital = AnesthesiaVitalSign::create(['anesthesia_monitoring_id' => $monitoring->id, 'recorded_at' => now()]);
        $this->assertInstanceOf(AnesthesiaMonitoring::class, $vital->anesthesiaMonitoring);
    }
}
