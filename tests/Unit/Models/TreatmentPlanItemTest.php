<?php

namespace Tests\Unit\Models;

use App\Models\TreatmentPlanItem;
use App\Models\TreatmentPlan;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TreatmentPlanItemTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-001', 'pet_id' => $pet->id, 'title' => 'Plano', 'status' => 'pending']);
        TreatmentPlanItem::create([
            'treatment_plan_id' => $tp->id, 'description' => 'Consulta',
            'category' => 'consulta', 'quantity' => 1.00, 'unit_price' => 150.00,
            'total' => 150.00, 'is_authorized' => true,
        ]);
        $this->assertDatabaseHas('treatment_plan_items', ['treatment_plan_id' => $tp->id, 'description' => 'Consulta']);
    }

    public function test_treatment_plan_relationship()
    {
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-002', 'pet_id' => $pet->id, 'title' => 'Plano', 'status' => 'pending']);
        $tpi = TreatmentPlanItem::create(['treatment_plan_id' => $tp->id, 'description' => 'Item', 'category' => 'exame', 'quantity' => 1]);
        $this->assertInstanceOf(TreatmentPlan::class, $tpi->treatmentPlan);
    }
}
