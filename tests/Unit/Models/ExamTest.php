<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Exam::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'blood_test',
            'status' => 'requested',
            'requested_date' => now(),
            'lab_name' => 'Lab A',
        ]);

        $this->assertDatabaseHas('exams', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'blood_test',
            'status' => 'requested',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $exam = Exam::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'blood_test',
            'status' => 'requested',
        ]);

        $this->assertInstanceOf(Pet::class, $exam->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $exam = Exam::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'blood_test',
            'status' => 'requested',
        ]);

        $this->assertInstanceOf(User::class, $exam->vet);
    }
}
