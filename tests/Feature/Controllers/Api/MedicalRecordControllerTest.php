<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class MedicalRecordControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_returns_paginated_records()
    {
        MedicalRecord::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/medical-records');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_index_filters_by_pet_id()
    {
        $pet = Pet::factory()->create();
        MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        MedicalRecord::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/medical-records?pet_id=' . $pet->id);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_show_returns_record()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->getJson('/api/v1/medical-records/' . $record->id);

        $response->assertOk();
        $response->assertJsonPath('id', $record->id);
    }

    public function test_show_returns_404_for_nonexistent_record()
    {
        $response = $this->getJson('/api/v1/medical-records/99999');

        $response->assertNotFound();
        $response->assertJson(['error' => 'Prontuário não encontrado']);
    }

    public function test_show_includes_relations()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->getJson('/api/v1/medical-records/' . $record->id);

        $response->assertOk();
        $response->assertJsonStructure(['pet', 'vet', 'prescriptions']);
    }

    public function test_by_pet_returns_records_for_pet()
    {
        $pet = Pet::factory()->create();
        MedicalRecord::factory()->count(3)->create(['pet_id' => $pet->id]);

        $response = $this->getJson('/api/v1/pets/' . $pet->id . '/medical-records');

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_by_pet_returns_empty_array_when_no_records()
    {
        $pet = Pet::factory()->create();

        $response = $this->getJson('/api/v1/pets/' . $pet->id . '/medical-records');

        $response->assertOk();
        $response->assertJson([]);
    }

    public function test_index_orders_by_date_desc()
    {
        $older = MedicalRecord::factory()->create(['date' => now()->subDays(5)]);
        $newer = MedicalRecord::factory()->create(['date' => now()]);

        $response = $this->getJson('/api/v1/medical-records');

        $response->assertOk();
        $this->assertEquals($newer->id, $response->json('data.0.id'));
    }
}
