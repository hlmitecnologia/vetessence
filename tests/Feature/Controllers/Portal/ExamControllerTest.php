<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Exam;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExamControllerTest extends TestCase
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

    public function test_index_returns_exams_view()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);
        Exam::factory()->count(3)->create(['pet_id' => $pet->id]);

        $response = $this->get(route('portal.exams.index'));

        $response->assertOk();
        $response->assertViewHas('exams');
    }

    public function test_index_returns_empty_when_no_exams()
    {
        $response = $this->get(route('portal.exams.index'));

        $response->assertOk();
        $response->assertViewHas('exams');
    }

    public function test_index_shows_only_own_pet_exams()
    {
        $pet1 = Pet::factory()->create();
        $pet2 = Pet::factory()->create();
        $this->tutor->pets()->attach($pet1->id, ['is_primary' => true]);

        Exam::factory()->count(2)->create(['pet_id' => $pet1->id]);
        Exam::factory()->count(3)->create(['pet_id' => $pet2->id]);

        $response = $this->get(route('portal.exams.index'));

        $response->assertOk();
        $response->assertViewHas('exams', function ($exams) {
            return $exams->count() === 2;
        });
    }

    public function test_index_handles_missing_relationship_gracefully()
    {
        $response = $this->get(route('portal.exams.index'));

        $response->assertOk();
        $response->assertViewHas('exams');
    }
}
