<?php

namespace App\Listeners;

use App\Events\ProcedurePerformed;
use App\Services\StockDeductionService;

class DeductStockListener
{
    protected $service;

    public function __construct(StockDeductionService $service)
    {
        $this->service = $service;
    }

    public function handle(ProcedurePerformed $event)
    {
        $this->service->deductFromVaccination($event->vaccination);
    }
}
