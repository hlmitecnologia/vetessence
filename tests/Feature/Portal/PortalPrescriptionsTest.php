<?php

namespace Tests\Feature\Portal;

use App\Models\Tutor;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortalPrescriptionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_shows_tutor_prescriptions()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id);
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);
        Prescription::factory()->create(['medical_record_id' => $record->id, 'medication' => 'Amoxicilina']);

        $other = Tutor::factory()->create();
        $otherPet = Pet::factory()->create();
        $otherPet->tutors()->attach($other->id);
        $otherRecord = MedicalRecord::factory()->create(['pet_id' => $otherPet->id]);
        Prescription::factory()->create(['medical_record_id' => $otherRecord->id, 'medication' => 'Dipirona']);

        $this->actingAs($tutor, 'tutor')->get(route('portal.prescriptions.index'))
            ->assertOk()
            ->assertSee('Amoxicilina')
            ->assertDontSee('Dipirona');
    }
}
