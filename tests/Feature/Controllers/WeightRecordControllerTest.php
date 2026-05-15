<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\WeightRecord;
use Tests\ModuleTestCase;

class WeightRecordControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('weight-records.index'));
        $response->assertOk();
    }

    public function test_store_creates_weight_record()
    {
        $pet = Pet::factory()->create();

        $response = $this->post(route('weight-records.store'), [
            'pet_id' => $pet->id,
            'weight' => 5.5,
            'measurement_date' => now()->format('Y-m-d'),
            'notes' => 'Peso atual',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('weight_records', [
            'pet_id' => $pet->id,
            'weight' => 5.5,
        ]);
    }

    public function test_show()
    {
        $record = WeightRecord::factory()->create();

        $response = $this->get(route('weight-records.show', $record));
        $response->assertOk();
    }

    public function test_update()
    {
        $record = WeightRecord::factory()->create();

        $response = $this->put(route('weight-records.update', $record), [
            'pet_id' => $record->pet_id,
            'weight' => 6.0,
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('weight_records', [
            'id' => $record->id,
            'weight' => 6.0,
        ]);
    }

    public function test_destroy()
    {
        $record = WeightRecord::factory()->create();

        $response = $this->delete(route('weight-records.destroy', $record));
        $response->assertRedirect();
        $this->assertDatabaseMissing('weight_records', ['id' => $record->id]);
    }
}
