<?php

namespace Tests\Unit\Models;

use App\Models\PreAnestheticEvaluation;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PreAnestheticEvaluationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $eval = PreAnestheticEvaluation::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'asa_score' => 2,
            'fasted' => true,
            'hydrated' => true,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('pre_anesthetic_evaluations', [
            'pet_id' => $pet->id,
            'asa_score' => 2,
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $eval = PreAnestheticEvaluation::factory()->create(['pet_id' => $pet->id]);
        $this->assertInstanceOf(Pet::class, $eval->pet);
    }

    public function test_vet_relationship()
    {
        $vet = User::factory()->create();
        $eval = PreAnestheticEvaluation::factory()->create(['vet_id' => $vet->id]);
        $this->assertInstanceOf(User::class, $eval->vet);
    }

    public function test_asa_scope()
    {
        PreAnestheticEvaluation::factory()->create(['asa_score' => 1]);
        PreAnestheticEvaluation::factory()->create(['asa_score' => 2]);
        $this->assertEquals(1, PreAnestheticEvaluation::where('asa_score', 1)->count());
    }
}
