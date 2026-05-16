<?php

namespace Tests\Unit\Models;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BankTransactionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $account = BankAccount::factory()->create();
        BankTransaction::create([
            'bank_account_id' => $account->id,
            'external_id' => 'EXT-001',
            'description' => 'Pagamento consulta',
            'amount' => 250.00,
            'transaction_date' => '2026-05-01',
            'type' => 'credit',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('bank_transactions', [
            'external_id' => 'EXT-001',
            'amount' => 250.00,
        ]);
    }

    public function test_bank_account_relationship()
    {
        $tx = BankTransaction::factory()->create();
        $this->assertInstanceOf(BankAccount::class, $tx->bankAccount);
    }

    public function test_scopes()
    {
        BankTransaction::factory()->count(2)->create(['status' => 'pending']);
        BankTransaction::factory()->create(['status' => 'reconciled']);
        BankTransaction::factory()->create(['status' => 'unmatched']);
        $this->assertCount(2, BankTransaction::pending()->get());
        $this->assertCount(1, BankTransaction::reconciled()->get());
        $this->assertCount(1, BankTransaction::unmatched()->get());
    }

    public function test_amount_cast()
    {
        $tx = BankTransaction::factory()->create(['amount' => 1234.56]);
        $this->assertEquals(1234.56, $tx->amount);
    }

    public function test_transaction_date_cast()
    {
        $tx = BankTransaction::factory()->create(['transaction_date' => '2026-05-01']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $tx->transaction_date);
    }
}
