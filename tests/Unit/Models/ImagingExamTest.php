<?php

namespace Tests\Unit\Models;

use App\Models\ImagingExam;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ImagingExamTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        ImagingExam::create([
            'exam_number' => 'IMG-20260515-0001', 'pet_id' => $pet->id, 'vet_id' => $vet->id,
            'exam_type' => 'rx', 'region' => 'torax', 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('imaging_exams', ['exam_number' => 'IMG-20260515-0001', 'status' => 'pending']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $ie = ImagingExam::create(['exam_number' => 'IMG-001', 'pet_id' => $pet->id, 'exam_type' => 'rx', 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $ie->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $ie = ImagingExam::create(['exam_number' => 'IMG-002', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'exam_type' => 'rx', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $ie->vet);
    }

    public function test_radiologist_relationship()
    {
        $pet = Pet::factory()->create();
        $radio = User::factory()->create();
        $ie = ImagingExam::create(['exam_number' => 'IMG-003', 'pet_id' => $pet->id, 'radiologist_id' => $radio->id, 'exam_type' => 'rx', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $ie->radiologist);
    }
}
