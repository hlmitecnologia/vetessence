<?php

namespace Tests\Feature\Commands;

use App\Models\InventoryReconciliation;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AlertaEstoqueCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_reports_variance_alerts()
    {
        InventoryReconciliation::factory()->create([
            'expected_quantity' => 100,
            'actual_quantity' => 80,
            'variance' => -20,
            'status' => 'pending',
        ]);

        $this->artisan('inventory:reconcile', ['--threshold' => 5])
            ->assertExitCode(1);
    }

    public function test_command_no_alerts_below_threshold()
    {
        InventoryReconciliation::factory()->create([
            'expected_quantity' => 100,
            'actual_quantity' => 98,
            'variance' => -2,
            'status' => 'pending',
        ]);

        $this->artisan('inventory:reconcile', ['--threshold' => 5])
            ->assertExitCode(0);
    }

    public function test_command_resolved_not_reported()
    {
        InventoryReconciliation::factory()->create([
            'expected_quantity' => 100,
            'actual_quantity' => 80,
            'variance' => -20,
            'status' => 'reconciled',
        ]);

        $this->artisan('inventory:reconcile', ['--threshold' => 5])
            ->assertExitCode(0);
    }
}
