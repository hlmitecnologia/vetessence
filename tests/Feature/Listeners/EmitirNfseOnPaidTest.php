<?php

namespace Tests\Feature\Listeners;

use App\Events\InvoicePaid;
use App\Listeners\EmitirNfseOnPaid;
use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Services\Nfse\NfseResult;
use App\Services\Nfse\NfseService;
use Illuminate\Support\Facades\Event;
use Tests\ModuleTestCase;

class EmitirNfseOnPaidTest extends ModuleTestCase
{
    public function test_emit_on_paid_with_active_config()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'none',
        ]);

        $service = $this->createMock(NfseService::class);
        $service->method('getConfig')->willReturn($config);
        $service->method('emitir')->willReturn(
            NfseResult::success('123456', 'COD', 'https://xml', 'https://pdf', 'RPS', 'CODE', [])
        );

        $listener = new EmitirNfseOnPaid($service);
        $listener->handle(new InvoicePaid($invoice));

        $this->assertTrue(true);
    }

    public function test_skip_emit_without_active_config()
    {
        $invoice = Invoice::factory()->create(['nfse_status' => 'none']);

        $service = $this->createMock(NfseService::class);
        $service->method('getConfig')->willReturn(null);
        $service->expects($this->never())->method('emitir');

        $listener = new EmitirNfseOnPaid($service);
        $listener->handle(new InvoicePaid($invoice));
    }
}
