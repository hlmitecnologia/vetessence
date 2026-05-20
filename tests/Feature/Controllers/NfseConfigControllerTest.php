<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\NfseConfig;
use Tests\ModuleTestCase;

class NfseConfigControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $branch = Branch::factory()->create();
        $this->loginAs('veterinario', ['branch_id' => $branch->id]);
    }

    public function test_edit()
    {
        $response = $this->get(route('nfse.config'));
        $response->assertOk();
    }

    public function test_update()
    {
        $response = $this->put(route('nfse.config.update'), [
            'cnpj' => '11.222.333/0001-44',
            'municipio_ibge' => '3550308',
            'regime_tributario' => 'simples_nacional',
            'serie' => '1',
            'ambiente' => 'homologacao',
            'webmania_app_id' => 'app-id',
            'webmania_app_secret' => 'app-secret',
            'webmania_consumer_key' => 'consumer-key',
            'webmania_consumer_secret' => 'consumer-secret',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nfse_configs', [
            'cnpj' => '11.222.333/0001-44',
            'regime_tributario' => 'simples_nacional',
        ]);
    }

    public function test_update_validates_required_fields()
    {
        $response = $this->put(route('nfse.config.update'), []);
        $response->assertSessionHasErrors(['cnpj', 'municipio_ibge', 'regime_tributario', 'ambiente']);
    }
}
