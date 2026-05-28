<?php

namespace Tests\Unit\Models;

use App\Models\NfseConfig;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseConfigTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $config = NfseConfig::factory()->create([
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('nfse_configs', [
            'id' => $config->id,
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
        ]);
    }

    public function test_fillable_does_not_include_old_fields()
    {
        $forbidden = ['branch_id', 'cnpj', 'municipio_ibge', 'regime_tributario', 'serie'];
        $fillable = (new NfseConfig)->getFillable();

        foreach ($forbidden as $field) {
            $this->assertNotContains($field, $fillable, "Field '$field' should not be in NfseConfig fillable");
        }
    }

    public function test_is_active_cast()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $this->assertTrue($config->is_active);
    }

    public function test_active_query()
    {
        NfseConfig::factory()->create(['is_active' => false]);
        NfseConfig::factory()->create(['is_active' => true]);

        $this->assertEquals(1, NfseConfig::where('is_active', true)->count());
    }

    public function test_no_relationships()
    {
        $methods = get_class_methods(NfseConfig::class);
        $relationshipMethods = ['branch', 'nfseInvoices', 'invoices'];
        foreach ($relationshipMethods as $method) {
            $this->assertNotContains($method, $methods, "NfseConfig should not have a '$method' relationship");
        }
    }

    public function test_no_custom_scopes()
    {
        $methods = get_class_methods(NfseConfig::class);
        $scopeMethods = array_filter($methods, fn ($m) => str_starts_with($m, 'scope'));
        $this->assertEmpty($scopeMethods, 'NfseConfig should not define any custom scopes');
    }

    public function test_provider_constants_available()
    {
        $fillable = (new NfseConfig)->getFillable();
        $providerFields = ['provider', 'focusnfe_token', 'spedy_api_key', 'spedy_api_secret', 'tecnospeed_token', 'nfeio_api_key'];
        foreach ($providerFields as $field) {
            $this->assertContains($field, $fillable, "Field '$field' should be in NfseConfig fillable");
        }
    }
}
