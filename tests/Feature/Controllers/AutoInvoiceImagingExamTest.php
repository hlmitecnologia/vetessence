<?php

namespace Tests\Feature\Controllers;

use App\Models\ImagingExam;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\ModuleTestCase;

class AutoInvoiceImagingExamTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_updating_to_completed_creates_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $exam = ImagingExam::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'scheduled',
        ]);

        $this->put(route('imaging-exams.update', $exam), [
            'pet_id' => $pet->id,
            'vet_id' => $exam->vet_id,
            'exam_type' => $exam->exam_type,
            'status' => 'completed',
            'exam_date' => now()->format('Y-m-d'),
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNotNull($invoice, 'Invoice should be created when imaging exam becomes completed');
        $this->assertEquals('pending', $invoice->status);
    }

    public function test_other_status_transitions_do_not_create_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $exam = ImagingExam::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'requested',
        ]);

        $this->put(route('imaging-exams.update', $exam), [
            'pet_id' => $pet->id,
            'vet_id' => $exam->vet_id,
            'exam_type' => $exam->exam_type,
            'status' => 'scheduled',
            'exam_date' => now()->format('Y-m-d'),
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNull($invoice, 'Invoice should not be created for non-completed status');
    }
}
