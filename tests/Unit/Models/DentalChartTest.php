<?php

namespace Tests\Unit\Models;

use App\Models\DentalChart;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DentalChartTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        DentalChart::create(['pet_id' => $pet->id, 'examination_date' => now(), 'procedure_type' => 'limpeza']);
        $this->assertDatabaseHas('dental_charts', ['pet_id' => $pet->id, 'procedure_type' => 'limpeza']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $dc = DentalChart::create(['pet_id' => $pet->id, 'examination_date' => now(), 'procedure_type' => 'limpeza']);
        $this->assertInstanceOf(Pet::class, $dc->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $dc = DentalChart::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'examination_date' => now(), 'procedure_type' => 'limpeza']);
        $this->assertInstanceOf(User::class, $dc->vet);
    }
}
