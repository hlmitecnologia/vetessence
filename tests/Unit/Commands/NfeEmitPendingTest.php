<?php

namespace Tests\Unit\Commands;

use App\Models\Invoice;
use App\Models\NfeConfig;
use App\Services\Nfe\NfeResult;
use App\Services\Nfe\NfeService;
use Tests\ModuleTestCase;

class NfeEmitPendingTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_emits_pending_nfe_invoices(): void
    {
        NfeConfig::create(['provider' => 'focusnfe', 'is_active' => true]);

        $invoice = Invoice::factory()->create(['nfe_status' => 'pending']);

        $this->app->bind(NfeService::class, function () use ($invoice) {
            $mock = \Mockery::mock(NfeService::class);
            $mock->shouldReceive('getConfig')->andReturn(NfeConfig::first());
            $mock->shouldReceive('emitir')
                ->once()
                ->with(\Mockery::on(fn ($arg) => $arg->id === $invoice->id))
                ->andReturn(NfeResult::success('NFE-001'));
            return $mock;
        });

        $this->artisan('nfe:emit-pending')->assertExitCode(0);
    }

    public function test_does_nothing_when_no_pending_invoices(): void
    {
        $this->artisan('nfe:emit-pending')
            ->expectsOutput('Nenhuma fatura pendente de emissão.')
            ->assertExitCode(0);
    }
}
