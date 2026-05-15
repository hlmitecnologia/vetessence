<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
            'complaint' => 'Coughing',
            'diagnosis' => 'Cold',
            'treatment' => 'Rest',
        ]);

        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'type' => 'consultation',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);

        $this->assertInstanceOf(Pet::class, $record->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);

        $this->assertInstanceOf(User::class, $record->vet);
    }

    public function test_appointment_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'appointment_id' => $appointment->id,
            'date' => now(),
            'type' => 'consultation',
        ]);

        $this->assertInstanceOf(Appointment::class, $record->appointment);
    }
}
