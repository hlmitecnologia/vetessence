<?php

namespace Tests\Unit\Listeners;

use App\Events\InvoicePaid;
use App\Listeners\EmitirNfeOnPaid;
use App\Models\Invoice;
use App\Models\NfeConfig;
use App\Models\NotificationLog;
use App\Services\Nfe\NfeResult;
use App\Services\Nfe\NfeService;
use Tests\ModuleTestCase;

class EmitirNfeOnPaidTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_handles_invoice_paid_event_and_emits_nfe(): void
    {
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Produto',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'product',
            'branch_id' => $invoice->branch_id,
        ]);

        NfeConfig::create(['provider' => 'focusnfe', 'is_active' => true]);

        $nfeService = \Mockery::mock(NfeService::class);
        $nfeService->shouldReceive('getConfig')->andReturn(NfeConfig::first());
        $nfeService->shouldReceive('emitirNfce')->once()->with($invoice)->andReturn(NfeResult::success('NFE-001'));

        $listener = new EmitirNfeOnPaid($nfeService);
        $listener->handle(new InvoicePaid($invoice));

        $this->addToAssertionCount(1);
    }

    public function test_skips_for_service_only_invoices(): void
    {
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Serviço',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'service',
            'branch_id' => $invoice->branch_id,
        ]);

        $nfeService = \Mockery::mock(NfeService::class);
        $nfeService->shouldNotReceive('emitirNfce');

        $listener = new EmitirNfeOnPaid($nfeService);
        $listener->handle(new InvoicePaid($invoice));

        $this->addToAssertionCount(1);
    }

    public function test_creates_notification_log_on_failure(): void
    {
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Produto',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'product',
            'branch_id' => $invoice->branch_id,
        ]);

        NfeConfig::create(['provider' => 'focusnfe', 'is_active' => true]);

        $nfeService = \Mockery::mock(NfeService::class);
        $nfeService->shouldReceive('getConfig')->andReturn(NfeConfig::first());
        $nfeService->shouldReceive('emitirNfce')->once()->with($invoice)->andReturn(NfeResult::error('Erro ao emitir'));

        $listener = new EmitirNfeOnPaid($nfeService);
        $listener->handle(new InvoicePaid($invoice));

        $this->assertDatabaseHas('notification_logs', [
            'tutor_id' => $invoice->tutor_id,
            'type' => 'nfe_emission_error',
            'channel' => 'system',
            'status' => 'failed',
        ]);
    }

    public function test_does_not_emit_if_config_is_missing(): void
    {
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Produto',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'product',
            'branch_id' => $invoice->branch_id,
        ]);

        $nfeService = \Mockery::mock(NfeService::class);
        $nfeService->shouldReceive('getConfig')->andReturn(null);
        $nfeService->shouldNotReceive('emitirNfce');

        $listener = new EmitirNfeOnPaid($nfeService);
        $listener->handle(new InvoicePaid($invoice));

        $this->addToAssertionCount(1);
    }
}
