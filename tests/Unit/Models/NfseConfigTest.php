<?php

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\NfseConfig;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseConfigTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $branch = Branch::factory()->create();
        $config = NfseConfig::factory()->create([
            'branch_id' => $branch->id,
            'cnpj' => '11.222.333/0001-44',
            'regime_tributario' => 'simples_nacional',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('nfse_configs', [
            'id' => $config->id,
            'cnpj' => '11.222.333/0001-44',
            'regime_tributario' => 'simples_nacional',
        ]);
    }

    public function test_is_active_cast()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $this->assertTrue($config->is_active);
    }

    public function test_branch_relationship()
    {
        $branch = Branch::factory()->create();
        $config = NfseConfig::factory()->create(['branch_id' => $branch->id]);
        $this->assertInstanceOf(Branch::class, $config->branch);
        $this->assertEquals($branch->id, $config->branch->id);
    }

    public function test_nfse_invoices_relationship()
    {
        $config = NfseConfig::factory()->create();
        $nfseInvoice = \App\Models\NfseInvoice::factory()->create([
            'branch_id' => $config->branch_id,
        ]);
        $this->assertCount(1, $config->nfseInvoices);
        $this->assertEquals($nfseInvoice->id, $config->nfseInvoices->first()->id);
    }

    public function test_active_scope()
    {
        NfseConfig::factory()->create(['is_active' => false]);
        NfseConfig::factory()->create(['is_active' => true]);

        $this->assertEquals(1, NfseConfig::where('is_active', true)->count());
    }
}
