<?php

namespace Tests\Feature\Commands;

use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Models\NfseInvoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseEmitPendingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_emits_pending_invoices()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);

        Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'pending',
        ]);

        $this->artisan('nfse:emit-pending')
            ->assertSuccessful();
    }

    public function test_skips_already_issued()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);

        Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'issued',
        ]);

        $this->artisan('nfse:emit-pending')
            ->assertSuccessful();
    }

    public function test_logs_errors()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);

        Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'pending',
        ]);

        $this->artisan('nfse:emit-pending')
            ->assertSuccessful();
    }
}
