<?php

namespace Tests\Unit\Models;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Branch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $branch = Branch::factory()->create();
        BankAccount::create([
            'branch_id' => $branch->id,
            'bank' => 'Banco do Brasil',
            'agency' => '1234-5',
            'account' => '67890-1',
            'account_type' => 'checking',
            'description' => 'Conta corrente principal',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('bank_accounts', [
            'bank' => 'Banco do Brasil',
            'agency' => '1234-5',
            'account' => '67890-1',
        ]);
    }

    public function test_branch_relationship()
    {
        $account = BankAccount::factory()->create();
        $this->assertInstanceOf(Branch::class, $account->branch);
    }

    public function test_transactions_relationship()
    {
        $account = BankAccount::factory()->create();
        BankTransaction::factory()->count(2)->create(['bank_account_id' => $account->id]);
        $this->assertCount(2, $account->transactions);
    }
}
