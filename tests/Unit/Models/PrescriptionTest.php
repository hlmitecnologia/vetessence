<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
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
        Prescription::create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicillin',
            'dosage' => '500',
            'unit' => 'mg',
            'frequency' => 'BID',
            'duration' => '7 days',
            'route' => 'oral',
            'instructions' => 'After meals',
        ]);

        $this->assertDatabaseHas('prescriptions', [
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicillin',
            'dosage' => '500',
        ]);
    }

    public function test_medicalRecord_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
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
        $prescription = Prescription::create([
            'medical_record_id' => $record->id,
            'medication' => 'Amoxicillin',
            'dosage' => '500',
            'unit' => 'mg',
            'frequency' => 'BID',
        ]);

        $this->assertInstanceOf(MedicalRecord::class, $prescription->medicalRecord);
    }
}
