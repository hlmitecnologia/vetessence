<?php

namespace Tests\Feature\Controllers;

use App\Models\Exam;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class ExamControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('exams.index'));
        $response->assertOk();
    }

    public function test_store_creates_exam()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('exams.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'Hemograma',
            'requested_date' => now()->format('Y-m-d'),
            'notes' => 'Jejum de 8h',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'pet_id' => $pet->id,
            'type' => 'Hemograma',
        ]);
    }

    public function test_show()
    {
        $exam = Exam::factory()->create();

        $response = $this->get(route('exams.show', $exam));
        $response->assertOk();
    }

    public function test_update()
    {
        $exam = Exam::factory()->create();

        $response = $this->put(route('exams.update', $exam), [
            'type' => 'Hemograma',
            'status' => 'ready',
            'result' => 'Resultado normal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'status' => 'ready',
        ]);
    }

    public function test_destroy()
    {
        $exam = Exam::factory()->create();

        $response = $this->delete(route('exams.destroy', $exam));
        $response->assertRedirect();
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }
}
