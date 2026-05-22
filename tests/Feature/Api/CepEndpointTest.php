<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CepEndpointTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_returns_address_data_for_valid_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'bairro' => 'Bela Vista',
                'localidade' => 'São Paulo',
                'uf' => 'SP',
                'ibge' => '3550308',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/cep/01310100');

        $response->assertStatus(200)
            ->assertJson([
                'zipcode' => '01310-100',
                'street' => 'Avenida Paulista',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]);
    }

    public function test_returns_404_for_invalid_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/99999999/json/' => Http::response(['erro' => true], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/cep/99999999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'CEP not found']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/cep/01310100');
        $response->assertStatus(401);
    }
}
