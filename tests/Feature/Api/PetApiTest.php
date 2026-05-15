<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_index_pets()
    {
        Pet::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/pets', $this->headers);
        $response->assertOk();
    }

    public function test_store_pet()
    {
        $tutor = Tutor::factory()->create();

        $response = $this->postJson('/api/v1/pets', [
            'name' => 'Rex',
            'tutor_id' => $tutor->id,
            'species' => 'canine',
            'gender' => 'male',
            'breed' => 'Labrador',
        ], $this->headers);

        $response->assertCreated()
            ->assertJson(['name' => 'Rex']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/pets', [], $this->headers);
        $response->assertStatus(422);
    }

    public function test_show_pet()
    {
        $pet = Pet::factory()->create();

        $response = $this->getJson("/api/v1/pets/{$pet->id}", $this->headers);
        $response->assertOk()
            ->assertJson(['id' => $pet->id]);
    }

    public function test_update_pet()
    {
        $pet = Pet::factory()->create();

        $response = $this->putJson("/api/v1/pets/{$pet->id}", [
            'name' => 'Updated Name',
        ], $this->headers);

        $response->assertOk();
        $this->assertEquals('Updated Name', $pet->fresh()->name);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/pets');
        $response->assertUnauthorized();
    }
}
