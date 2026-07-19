<?php

namespace Tests\Unit\Models;

use App\Models\CommissionLog;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommissionLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $log = CommissionLog::factory()->create([
            'description' => 'Comissão consulta',
            'base_value' => 500.00,
            'commission_value' => 50.00,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('commission_logs', [
            'id' => $log->id,
            'description' => 'Comissão consulta',
            'commission_value' => 50.00,
        ]);
    }

    public function test_user_relationship()
    {
        $log = CommissionLog::factory()->create();
        $this->assertInstanceOf(User::class, $log->user);
    }

    public function test_invoice_relationship()
    {
        $log = CommissionLog::factory()->create();
        $this->assertInstanceOf(Invoice::class, $log->invoice);
    }

    public function test_scopes()
    {
        $ids = [];
        $ids[] = CommissionLog::factory()->create(['status' => 'pending'])->id;
        $ids[] = CommissionLog::factory()->create(['status' => 'pending'])->id;
        $ids[] = CommissionLog::factory()->create(['status' => 'paid'])->id;
        $this->assertCount(2, CommissionLog::whereIn('id', $ids)->pending()->get());
        $this->assertCount(1, CommissionLog::whereIn('id', $ids)->paid()->get());
    }
}
