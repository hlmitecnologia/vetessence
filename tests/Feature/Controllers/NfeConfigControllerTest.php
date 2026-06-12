<?php

namespace Tests\Feature\Controllers;

use App\Models\NfeConfig;
use Tests\ModuleTestCase;

class NfeConfigControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_edit()
    {
        NfeConfig::create([
            'provider' => 'focusnfe',
            'ambiente' => 'homologacao',
            'focusnfe_token' => 'test-token',
            'is_active' => true,
        ]);

        $response = $this->get(route('nfe.config'));
        $response->assertOk();
    }

    public function test_edit_returns_view_when_no_config_exists()
    {
        $response = $this->get(route('nfe.config'));
        $response->assertOk();
    }

    public function test_update_with_focusnfe()
    {
        $response = $this->put(route('nfe.config.update'), [
            'provider' => 'focusnfe',
            'ambiente' => 'homologacao',
            'focusnfe_token' => 'fn-token-123',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('nfe_configs', [
            'provider' => 'focusnfe',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);
    }

    public function test_update_with_nfeio()
    {
        $response = $this->put(route('nfe.config.update'), [
            'provider' => 'nfeio',
            'ambiente' => 'producao',
            'nfeio_api_key' => 'nfeio-api-key-456',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nfe_configs', [
            'provider' => 'nfeio',
            'ambiente' => 'producao',
            'is_active' => true,
        ]);
    }

    public function test_update_with_webmania()
    {
        $response = $this->put(route('nfe.config.update'), [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'webmania_app_id' => 'app-id',
            'webmania_app_secret' => 'app-secret',
            'webmania_consumer_key' => 'consumer-key',
            'webmania_consumer_secret' => 'consumer-secret',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nfe_configs', [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);
    }

    public function test_update_validates_required_fields()
    {
        $response = $this->put(route('nfe.config.update'), []);
        $response->assertSessionHasErrors(['provider', 'ambiente']);
    }

    public function test_update_validates_provider_specific_fields()
    {
        $response = $this->put(route('nfe.config.update'), [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
        ]);
        $response->assertSessionHasErrors([
            'webmania_app_id',
            'webmania_app_secret',
            'webmania_consumer_key',
            'webmania_consumer_secret',
        ]);
    }

    public function test_update_validates_provider_enum()
    {
        $response = $this->put(route('nfe.config.update'), [
            'provider' => 'invalid_provider',
            'ambiente' => 'homologacao',
        ]);
        $response->assertSessionHasErrors('provider');
    }
}
