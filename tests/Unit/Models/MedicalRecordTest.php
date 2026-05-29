<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Tutor;
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
            'time' => '10:00',
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

    public function test_invoices_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'user_id' => $vet->id,
            'medical_record_id' => $record->id,
            'total' => 100,
            'subtotal' => 100,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertTrue($record->invoices->contains($invoice));
    }

    public function test_has_generated_paid_invoice_returns_true_when_direct()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);
        Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'user_id' => $vet->id,
            'medical_record_id' => $record->id,
            'total' => 100,
            'subtotal' => 100,
            'status' => 'paid',
            'paid_at' => now(),
            'due_date' => now(),
        ]);

        $this->assertTrue($record->hasGeneratedPaidInvoice());
    }

    public function test_has_generated_paid_invoice_returns_false_when_pending()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);
        Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'user_id' => $vet->id,
            'medical_record_id' => $record->id,
            'total' => 100,
            'subtotal' => 100,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertFalse($record->hasGeneratedPaidInvoice());
    }

    public function test_has_generated_paid_invoice_returns_true_when_appointment_has_paid()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'completed',
        ]);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'user_id' => $vet->id,
            'total' => 100,
            'subtotal' => 100,
            'status' => 'paid',
            'paid_at' => now(),
            'due_date' => now(),
        ]);
        $appointment->invoices()->attach($invoice->id);
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'appointment_id' => $appointment->id,
            'date' => now(),
            'type' => 'consultation',
        ]);

        $this->assertTrue($record->hasGeneratedPaidInvoice());
    }

    public function test_has_generated_paid_invoice_returns_false_without_invoices()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'user_id' => $vet->id,
            'date' => now(),
            'type' => 'consultation',
        ]);

        $this->assertFalse($record->hasGeneratedPaidInvoice());
    }
}
