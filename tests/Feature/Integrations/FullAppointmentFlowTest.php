<?php

namespace Tests\Feature\Integrations;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class FullAppointmentFlowTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->vet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_complete_appointment_flow()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $this->vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'scheduled',
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('appointments', [
            'pet_id' => $pet->id,
            'vet_id' => $this->vet->id,
            'status' => 'scheduled',
        ]);

        $mrResponse = $this->post(route('medical-records.store'), [
            'pet_id' => $pet->id,
            'user_id' => $this->vet->id,
            'appointment_id' => $appointment->id,
            'date' => now()->format('Y-m-d'),
            'type' => 'consultation',
            'complaint' => 'Tosse persistente',
            'diagnosis' => 'Bronquite',
            'treatment' => 'Antibiótico e repouso',
            'branch_id' => $this->branch->id,
        ]);
        $mrResponse->assertSessionDoesntHaveErrors();
        $mrResponse->assertRedirect();

        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'diagnosis' => 'Bronquite',
        ]);
        $medicalRecord = MedicalRecord::where('pet_id', $pet->id)->first();

        $prescriptionResponse = $this->post(route('prescriptions.store'), [
            'medical_record_id' => $medicalRecord->id,
            'medication' => 'Amoxicilina',
            'dosage' => '500',
            'unit' => 'mg',
            'frequency' => '8/8h',
            'duration' => '7 dias',
            'route' => 'oral',
            'instructions' => 'Tomar após as refeições',
            'branch_id' => $this->branch->id,
        ]);
        $prescriptionResponse->assertSessionDoesntHaveErrors();
        $prescriptionResponse->assertRedirect();

        $this->assertDatabaseHas('prescriptions', [
            'medical_record_id' => $medicalRecord->id,
            'medication' => 'Amoxicilina',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 250.00,
            'total' => 250.00,
            'status' => 'pending',
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'total' => 250.00,
            'status' => 'pending',
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'credit_card',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'payment_method' => 'credit_card',
        ]);
        $this->assertNotNull($invoice->fresh()->paid_at);
    }
}
