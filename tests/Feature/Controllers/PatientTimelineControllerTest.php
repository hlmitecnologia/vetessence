<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use App\Models\Appointment;
use App\Models\Exam;
use App\Models\Surgery;
use App\Models\Hospitalization;
use App\Models\Invoice;
use Tests\ModuleTestCase;

class PatientTimelineControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_shows_timeline()
    {
        $pet = Pet::factory()->create();
        MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
    }

    public function test_index_shows_medical_record_events()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'diagnosis' => 'Diagnóstico teste',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Diagnóstico teste');
    }

    public function test_index_shows_vaccination_events()
    {
        $pet = Pet::factory()->create();
        Vaccination::factory()->create([
            'pet_id' => $pet->id,
            'vaccine' => 'V10',
            'batch' => 'B123',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('V10');
        $response->assertSee('B123');
    }

    public function test_index_shows_appointment_events()
    {
        $pet = Pet::factory()->create();
        Appointment::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'scheduled',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
    }

    public function test_index_shows_exam_events()
    {
        $pet = Pet::factory()->create();
        Exam::factory()->create([
            'pet_id' => $pet->id,
            'type' => 'Hemograma',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Hemograma');
    }

    public function test_index_shows_surgery_events()
    {
        $pet = Pet::factory()->create();
        Surgery::factory()->create([
            'pet_id' => $pet->id,
            'surgery_type' => 'Castração',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Castração');
    }

    public function test_index_shows_hospitalization_events()
    {
        $pet = Pet::factory()->create();
        Hospitalization::factory()->create([
            'pet_id' => $pet->id,
            'admission_reason' => 'Observação',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
        $response->assertSee('Internação');
    }

    public function test_index_shows_invoice_events()
    {
        $pet = Pet::factory()->create();
        Invoice::factory()->create([
            'pet_id' => $pet->id,
            'total' => 250.00,
            'status' => 'paid',
        ]);
        $response = $this->get(route('pets.timeline', $pet));
        $response->assertOk();
    }
}
