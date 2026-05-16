<?php

namespace Tests\Feature\Portal;

use App\Models\Tutor;
use App\Models\Pet;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortalMedicalRecordsTest extends TestCase
{
    use DatabaseTransactions;

    private function createPetForTutor(Tutor $tutor): Pet
    {
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id);
        return $pet;
    }

    public function test_index_shows_tutor_medical_records()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->createPetForTutor($tutor);
        MedicalRecord::factory()->create(['pet_id' => $pet->id, 'diagnosis' => 'Otite']);

        $otherTutor = Tutor::factory()->create();
        $otherPet = $this->createPetForTutor($otherTutor);
        MedicalRecord::factory()->create(['pet_id' => $otherPet->id, 'diagnosis' => 'Outro']);

        $this->actingAs($tutor, 'tutor')->get(route('portal.medical-records.index'))
            ->assertOk()
            ->assertSee('Otite')
            ->assertDontSee('Outro');
    }

    public function test_show_pet_medical_records()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->createPetForTutor($tutor);
        MedicalRecord::factory()->create(['pet_id' => $pet->id, 'diagnosis' => 'Dermatite']);

        $this->actingAs($tutor, 'tutor')->get(route('portal.medical-records.show', $pet->id))
            ->assertOk()
            ->assertSee('Dermatite');
    }

    public function test_unauthorized_pet_returns_404()
    {
        $tutor = Tutor::factory()->create();
        $otherPet = Pet::factory()->create();

        $this->actingAs($tutor, 'tutor')
            ->get(route('portal.medical-records.show', $otherPet->id))
            ->assertStatus(404);
    }
}
