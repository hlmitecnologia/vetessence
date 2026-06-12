<?php

namespace Tests\Unit\Listeners;

use App\Events\ProcedurePerformed;
use App\Listeners\DeductStockListener;
use App\Models\Vaccination;
use App\Services\StockDeductionService;
use Tests\ModuleTestCase;

class DeductStockListenerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_handles_event_and_deducts_stock(): void
    {
        $vaccination = Vaccination::factory()->create();

        $service = \Mockery::mock(StockDeductionService::class);
        $service->shouldReceive('deductFromVaccination')
            ->once()
            ->with($vaccination);

        $listener = new DeductStockListener($service);
        $listener->handle(new ProcedurePerformed($vaccination));

        $this->addToAssertionCount(1);
    }

    public function test_handles_event_data_correctly(): void
    {
        $vaccination = Vaccination::factory()->create([
            'vaccine' => 'Vacina Antirrábica',
        ]);

        $service = \Mockery::mock(StockDeductionService::class);
        $service->shouldReceive('deductFromVaccination')
            ->once()
            ->with(\Mockery::on(function ($arg) use ($vaccination) {
                return $arg->id === $vaccination->id
                    && $arg->vaccine === 'Vacina Antirrábica';
            }));

        $listener = new DeductStockListener($service);
        $listener->handle(new ProcedurePerformed($vaccination));

        $this->addToAssertionCount(1);
    }
}
