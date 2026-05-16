<?php

namespace Tests\Feature;

use App\Models\Vaccination;
use App\Models\Pet;
use Tests\ModuleTestCase;

class VaccinationForecastTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_forecast_page_loads()
    {
        $response = $this->get(route('vaccinations.forecast'));
        $response->assertOk();
    }

    public function test_forecast_shows_upcoming_vaccines()
    {
        $pet = Pet::factory()->create();
        Vaccination::factory()->create([
            'pet_id' => $pet->id,
            'next_date' => now()->addDays(10),
        ]);
        $response = $this->get(route('vaccinations.forecast', ['days' => 30]));
        $response->assertOk();
        $response->assertSee($pet->name);
    }
}
