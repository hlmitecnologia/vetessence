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
        NfseConfig::query()->update(['is_active' => false]);
        $response = $this->put(route('nfse.config.update'), [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'webmania_access_token' => 'access-token',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nfse_configs', [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);
    }

    public function test_update_validates_required_fields()
    {
        $response = $this->put(route('nfse.config.update'), []);
        $response->assertSessionHasErrors(['provider', 'ambiente']);
    }

    public function test_update_with_nfeio()
    {
        $response = $this->put(route('nfse.config.update'), [
            'provider' => 'nfeio',
            'ambiente' => 'homologacao',
            'nfeio_api_key' => 'nfeio-api-key-789',
            'nfeio_company_id' => 'company-456',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('nfse_configs', [
            'provider' => 'nfeio',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);
    }

    public function test_update_nfeio_validates_company_id()
    {
        $response = $this->put(route('nfse.config.update'), [
            'provider' => 'nfeio',
            'ambiente' => 'homologacao',
            'nfeio_api_key' => 'key',
        ]);
        $response->assertSessionHasErrors(['nfeio_company_id']);
    }
}
