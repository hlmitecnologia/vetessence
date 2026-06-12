<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Pet;
use App\Models\Vaccination;
use Tests\ModuleTestCase;

class VaccinationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_returns_paginated_vaccinations()
    {
        Vaccination::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/vaccinations');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_index_filters_by_pet()
    {
        $pet = Pet::factory()->create();
        Vaccination::factory()->count(2)->create(['pet_id' => $pet]);
        Vaccination::factory()->create();

        $response = $this->getJson('/api/v1/vaccinations?pet_id=' . $pet->id);

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_show_returns_vaccination()
    {
        $vaccination = Vaccination::factory()->create();

        $response = $this->getJson('/api/v1/vaccinations/' . $vaccination->id);

        $response->assertOk()
            ->assertJson(['id' => $vaccination->id]);
    }

    public function test_show_returns_404_for_nonexistent_vaccination()
    {
        $response = $this->getJson('/api/v1/vaccinations/99999');

        $response->assertNotFound()
            ->assertJson(['error' => 'Vacinação não encontrada']);
    }

    public function test_store_creates_vaccination()
    {
        $pet = Pet::factory()->create();

        $response = $this->postJson('/api/v1/vaccinations', [
            'pet_id' => $pet->id,
            'vaccine' => 'V10',
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertCreated()
            ->assertJson(['vaccine' => 'V10']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/vaccinations', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['pet_id', 'vaccine', 'date']);
    }

    public function test_store_validates_pet_exists()
    {
        $response = $this->postJson('/api/v1/vaccinations', [
            'pet_id' => 99999,
            'vaccine' => 'V10',
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['pet_id']);
    }

    public function test_by_pet_returns_vaccinations()
    {
        $pet = Pet::factory()->create();
        Vaccination::factory()->count(2)->create(['pet_id' => $pet]);

        $response = $this->getJson('/api/v1/pets/' . $pet->id . '/vaccinations');

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function test_by_pet_returns_empty_when_no_vaccinations()
    {
        $pet = Pet::factory()->create();

        $response = $this->getJson('/api/v1/pets/' . $pet->id . '/vaccinations');

        $response->assertOk()
            ->assertJsonCount(0);
    }
}
