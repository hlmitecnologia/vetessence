<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\Teleconsultation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeleconsultationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = User::factory()->create();
        $vet = User::factory()->create();

        Teleconsultation::create([
            'room_name' => 'Sala 1',
            'room_token' => 'ABC-1234',
            'appointment_id' => null,
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'provider' => 'zoom',
            'provider_room_id' => 'room_123',
            'provider_url' => 'https://zoom.us/j/123',
            'scheduled_at' => now(),
            'started_at' => null,
            'ended_at' => null,
            'duration_minutes' => 30,
            'notes' => 'observacao',
            'recording_url' => null,
            'branch_id' => null,
        ]);

        $this->assertDatabaseHas('teleconsultations', [
            'room_name' => 'Sala 1',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'duration_minutes' => 30,
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $teleconsultation = Teleconsultation::create([
            'room_name' => 'Sala',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        $this->assertInstanceOf(Pet::class, $teleconsultation->pet);
        $this->assertEquals($pet->id, $teleconsultation->pet->id);
    }

    public function test_tutor_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = User::factory()->create();
        $vet = User::factory()->create();
        $teleconsultation = Teleconsultation::create([
            'room_name' => 'Sala',
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $teleconsultation->tutor);
        $this->assertEquals($tutor->id, $teleconsultation->tutor->id);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $teleconsultation = Teleconsultation::create([
            'room_name' => 'Sala',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $teleconsultation->vet);
        $this->assertEquals($vet->id, $teleconsultation->vet->id);
    }

    public function test_active_scope()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'ACT-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'active', 'scheduled_at' => now()]);
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'SCH-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'scheduled', 'scheduled_at' => now()]);
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'CAN-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'canceled', 'scheduled_at' => now()]);

        $this->assertCount(1, Teleconsultation::active()->get());
    }

    public function test_scheduled_scope()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'SCH1-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'scheduled', 'scheduled_at' => now()]);
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'ACT-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'active', 'scheduled_at' => now()]);
        Teleconsultation::create(['room_name' => 'Sala', 'room_token' => 'SCH2-' . uniqid(), 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'status' => 'scheduled', 'scheduled_at' => now()]);

        $this->assertCount(2, Teleconsultation::scheduled()->get());
    }

    public function test_room_url_accessor_with_provider_url()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $teleconsultation = Teleconsultation::create([
            'room_name' => 'Sala',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'provider_url' => 'https://zoom.us/j/abc',
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        $this->assertEquals('https://zoom.us/j/abc', $teleconsultation->room_url);
    }

    public function test_room_url_accessor_without_provider_url()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $teleconsultation = Teleconsultation::create([
            'room_name' => 'Sala',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'room_token' => 'TOKEN123',
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        $this->assertStringContainsString('/teleconsultation/TOKEN123', $teleconsultation->room_url);
    }
}
