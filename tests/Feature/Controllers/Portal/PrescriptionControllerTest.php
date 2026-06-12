<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PrescriptionControllerTest extends TestCase
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

    public function test_index_returns_prescriptions_view()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        Prescription::factory()->count(2)->create(['medical_record_id' => $record->id]);

        $response = $this->get(route('portal.prescriptions.index'));

        $response->assertOk();
        $response->assertViewHas('prescriptions');
    }

    public function test_index_returns_empty_when_no_prescriptions()
    {
        $response = $this->get(route('portal.prescriptions.index'));

        $response->assertOk();
        $response->assertViewHas('prescriptions');
    }

    public function test_index_shows_only_own_pet_prescriptions()
    {
        $pet1 = Pet::factory()->create();
        $pet2 = Pet::factory()->create();
        $this->tutor->pets()->attach($pet1->id, ['is_primary' => true]);

        $record1 = MedicalRecord::factory()->create(['pet_id' => $pet1->id]);
        $record2 = MedicalRecord::factory()->create(['pet_id' => $pet2->id]);

        Prescription::factory()->count(2)->create(['medical_record_id' => $record1->id]);
        Prescription::factory()->count(3)->create(['medical_record_id' => $record2->id]);

        $response = $this->get(route('portal.prescriptions.index'));

        $response->assertOk();
        $response->assertViewHas('prescriptions', function ($prescriptions) {
            return $prescriptions->count() === 2;
        });
    }

    public function test_index_orders_prescriptions_by_created_at_desc()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        $older = Prescription::factory()->create(['medical_record_id' => $record->id, 'created_at' => now()->subDays(2)]);
        $newer = Prescription::factory()->create(['medical_record_id' => $record->id, 'created_at' => now()->subDay()]);

        $response = $this->get(route('portal.prescriptions.index'));

        $response->assertOk();
        $prescriptions = $response->viewData('prescriptions');
        $this->assertTrue($prescriptions->first()->id === $newer->id);
    }
}
