<?php

namespace Tests\Unit\Models;

use App\Models\TreatmentPlan;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TreatmentPlanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        TreatmentPlan::create([
            'plan_number' => 'PLN-2026-00001', 'pet_id' => $pet->id,
            'tutor_id' => $tutor->id, 'vet_id' => $vet->id,
            'title' => 'Tratamento', 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('treatment_plans', ['plan_number' => 'PLN-2026-00001', 'title' => 'Tratamento']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-001', 'pet_id' => $pet->id, 'title' => 'Teste', 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $tp->pet);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-002', 'pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'title' => 'Teste', 'status' => 'pending']);
        $this->assertInstanceOf(Tutor::class, $tp->tutor);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-003', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'title' => 'Teste', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $tp->vet);
    }
}
