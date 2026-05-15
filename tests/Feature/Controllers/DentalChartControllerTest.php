<?php

namespace Tests\Feature\Controllers;

use App\Models\DentalChart;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class DentalChartControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('dental-charts.index'));
        $response->assertOk();
    }

    public function test_store_creates_dental_chart()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('dental-charts.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'examination_date' => now()->format('Y-m-d'),
            'general_notes' => 'Tártaro grau 2',
            'procedure_type' => 'consultation',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dental_charts', [
            'pet_id' => $pet->id,
            'general_notes' => 'Tártaro grau 2',
        ]);
    }

    public function test_show()
    {
        $chart = DentalChart::factory()->create();

        $response = $this->get(route('dental-charts.show', $chart));
        $response->assertOk();
    }

    public function test_update()
    {
        $chart = DentalChart::factory()->create();

        $response = $this->put(route('dental-charts.update', $chart), [
            'pet_id' => $chart->pet_id,
            'vet_id' => $chart->vet_id,
            'examination_date' => now()->format('Y-m-d'),
            'general_notes' => 'Notas atualizadas',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dental_charts', [
            'id' => $chart->id,
            'general_notes' => 'Notas atualizadas',
        ]);
    }

    public function test_destroy()
    {
        $chart = DentalChart::factory()->create();

        $response = $this->delete(route('dental-charts.destroy', $chart));
        $response->assertRedirect();
        $this->assertDatabaseMissing('dental_charts', ['id' => $chart->id]);
    }
}
