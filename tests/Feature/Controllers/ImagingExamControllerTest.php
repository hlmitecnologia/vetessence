<?php

namespace Tests\Feature\Controllers;

use App\Models\ImagingExam;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class ImagingExamControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('imaging-exams.index'));
        $response->assertOk();
    }

    public function test_store_creates_imaging_exam()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('imaging-exams.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'exam_type' => 'xray',
            'exam_date' => now()->format('Y-m-d'),
            'findings' => 'Fratura exposta',
            'status' => 'requested',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('imaging_exams', [
            'pet_id' => $pet->id,
            'exam_type' => 'xray',
        ]);
    }

    public function test_show()
    {
        $exam = ImagingExam::factory()->create();

        $response = $this->get(route('imaging-exams.show', $exam));
        $response->assertOk();
    }

    public function test_update()
    {
        $exam = ImagingExam::factory()->create();

        $response = $this->put(route('imaging-exams.update', $exam), [
            'pet_id' => $exam->pet_id,
            'vet_id' => $exam->vet_id,
            'exam_type' => $exam->exam_type,
            'exam_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'findings' => 'Resultado normal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('imaging_exams', [
            'id' => $exam->id,
            'status' => 'completed',
        ]);
    }

    public function test_destroy()
    {
        $exam = ImagingExam::factory()->create();

        $response = $this->delete(route('imaging-exams.destroy', $exam));
        $response->assertRedirect();
        $this->assertDatabaseMissing('imaging_exams', ['id' => $exam->id]);
    }
}
