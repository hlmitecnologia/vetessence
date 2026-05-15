<?php

namespace Tests\Unit\Models;

use App\Models\AnesthesiaMonitoring;
use App\Models\Pet;
use App\Models\User;
use App\Models\Surgery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnesthesiaMonitoringTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'scheduled_date' => now(), 'surgery_type' => 'castracao', 'status' => 'scheduled']);
        AnesthesiaMonitoring::create([
            'surgery_id' => $surgery->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id,
            'monitoring_start' => now(), 'monitoring_end' => now()->addHour(),
        ]);
        $this->assertDatabaseHas('anesthesia_monitorings', ['surgery_id' => $surgery->id, 'pet_id' => $pet->id]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'scheduled_date' => now(), 'surgery_type' => 'castracao', 'status' => 'scheduled']);
        $monitoring = AnesthesiaMonitoring::create(['surgery_id' => $surgery->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id]);
        $this->assertInstanceOf(Pet::class, $monitoring->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'scheduled_date' => now(), 'surgery_type' => 'castracao', 'status' => 'scheduled']);
        $monitoring = AnesthesiaMonitoring::create(['surgery_id' => $surgery->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id]);
        $this->assertInstanceOf(User::class, $monitoring->vet);
    }
}
