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

    public function test_is_active_cast()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $this->assertTrue($config->is_active);
    }

    public function test_active_scope()
    {
        NfseConfig::factory()->create(['is_active' => false]);
        NfseConfig::factory()->create(['is_active' => true]);

        $this->assertEquals(1, NfseConfig::where('is_active', true)->count());
    }
}
