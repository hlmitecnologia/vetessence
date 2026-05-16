<?php

namespace Tests\Feature\Portal;

use App\Models\Tutor;
use App\Models\Pet;
use App\Models\Exam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortalExamsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_shows_tutor_exams()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id);
        Exam::factory()->create(['pet_id' => $pet->id, 'type' => 'Ultrassom']);

        $other = Tutor::factory()->create();
        $otherPet = Pet::factory()->create();
        $otherPet->tutors()->attach($other->id);
        Exam::factory()->create(['pet_id' => $otherPet->id, 'type' => 'Raio-X']);

        $this->actingAs($tutor, 'tutor')->get(route('portal.exams.index'))
            ->assertOk()
            ->assertSee('Ultrassom')
            ->assertDontSee('Raio-X');
    }
}
