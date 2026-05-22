<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CitiesEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private function actingAsUser(): User
    {
        $branch = Branch::create(['name' => 'Matriz', 'slug' => 'matriz']);
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $this->actingAs($user);
        return $user;
    }

    public function test_returns_cities_for_valid_state()
    {
        $this->actingAsUser();
        $state = State::factory()->create(['uf' => 'SP']);
        City::factory()->create(['state_id' => $state->id, 'name' => 'São Paulo']);
        City::factory()->create(['state_id' => $state->id, 'name' => 'Campinas']);

        $response = $this->getJson("/api/cities/{$state->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['São Paulo']);
        $response->assertJsonFragment(['Campinas']);
    }

    public function test_returns_empty_for_invalid_state()
    {
        $this->actingAsUser();
        $response = $this->getJson('/api/cities/99999');

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
}
