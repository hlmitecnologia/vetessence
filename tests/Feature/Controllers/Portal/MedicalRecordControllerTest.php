<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MedicalRecordControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tutor = Tutor::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($this->tutor, 'tutor');
    }

    public function test_index_returns_medical_records_view()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);
        MedicalRecord::factory()->count(2)->create(['pet_id' => $pet->id]);

        $response = $this->get(route('portal.medical-records.index'));

        $response->assertOk();
        $response->assertViewHas('records');
    }

    public function test_index_returns_empty_when_no_records()
    {
        $response = $this->get(route('portal.medical-records.index'));

        $response->assertOk();
        $response->assertViewHas('records');
    }

    public function test_index_shows_only_own_pet_records()
    {
        $pet1 = Pet::factory()->create();
        $pet2 = Pet::factory()->create();
        $this->tutor->pets()->attach($pet1->id, ['is_primary' => true]);

        MedicalRecord::factory()->count(2)->create(['pet_id' => $pet1->id]);
        MedicalRecord::factory()->count(3)->create(['pet_id' => $pet2->id]);

        $response = $this->get(route('portal.medical-records.index'));

        $response->assertOk();
        $response->assertViewHas('records', function ($records) {
            return $records->count() === 2;
        });
    }

    public function test_show_returns_medical_record_for_own_pet()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);

        $response = $this->get(route('portal.medical-records.show', $pet->id));

        $response->assertOk();
        $response->assertViewHas('pet');
    }

    public function test_show_returns_404_for_unowned_pet()
    {
        $pet = Pet::factory()->create();

        $response = $this->get(route('portal.medical-records.show', $pet->id));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_nonexistent_pet()
    {
        $response = $this->get(route('portal.medical-records.show', 99999));

        $response->assertNotFound();
    }

    public function test_index_orders_records_by_created_at_desc()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);
        $older = MedicalRecord::factory()->create(['pet_id' => $pet->id, 'created_at' => now()->subDays(2)]);
        $newer = MedicalRecord::factory()->create(['pet_id' => $pet->id, 'created_at' => now()->subDay()]);

        $response = $this->get(route('portal.medical-records.index'));

        $response->assertOk();
        $records = $response->viewData('records');
        $this->assertTrue($records->first()->id === $newer->id);
    }
}
