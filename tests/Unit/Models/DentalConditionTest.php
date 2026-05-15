<?php

namespace Tests\Unit\Models;

use App\Models\DentalCondition;
use App\Models\DentalChart;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DentalConditionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $chart = DentalChart::create(['pet_id' => $pet->id, 'examination_date' => now(), 'procedure_type' => 'limpeza']);
        DentalCondition::create([
            'dental_chart_id' => $chart->id, 'tooth_number' => 101,
            'quadrant' => 'superior_direito', 'condition' => 'fratura', 'severity' => 'moderada',
        ]);
        $this->assertDatabaseHas('dental_conditions', ['dental_chart_id' => $chart->id, 'tooth_number' => 101]);
    }

    public function test_dental_chart_relationship()
    {
        $pet = Pet::factory()->create();
        $chart = DentalChart::create(['pet_id' => $pet->id, 'examination_date' => now(), 'procedure_type' => 'limpeza']);
        $dc = DentalCondition::create(['dental_chart_id' => $chart->id, 'condition' => 'fratura', 'tooth_number' => 101, 'quadrant' => 'superior_direito', 'severity' => 'leve']);
        $this->assertInstanceOf(DentalChart::class, $dc->dentalChart);
    }
}
