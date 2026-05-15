<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetControllerTest extends TestCase
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

    public function test_index()
    {
        $response = $this->get(route('portal.pets.index'));
        $response->assertOk();
    }

    public function test_show()
    {
        $pet = Pet::factory()->create();
        $this->tutor->pets()->attach($pet->id, ['is_primary' => true]);

        $response = $this->get(route('portal.pets.show', $pet->id));
        $response->assertOk();
    }
}
