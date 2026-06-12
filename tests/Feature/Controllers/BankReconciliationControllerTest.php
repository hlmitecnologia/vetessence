<?php

namespace Tests\Feature\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use Tests\ModuleTestCase;

class BankReconciliationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        BankTransaction::factory()->count(3)->create();

        $response = $this->get(route('bank-reconciliation.index'));

        $response->assertOk();
    }

    public function test_index_filters_by_bank_account()
    {
        $account = BankAccount::factory()->create();
        BankTransaction::factory()->create(['bank_account_id' => $account->id]);

        $response = $this->get(route('bank-reconciliation.index', ['bank_account_id' => $account->id]));

        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        BankTransaction::factory()->create(['status' => 'reconciled']);

        $response = $this->get(route('bank-reconciliation.index', ['status' => 'reconciled']));

        $response->assertOk();
    }

    public function test_index_filters_by_date_range()
    {
        BankTransaction::factory()->create(['transaction_date' => now()->subMonth()]);

        $response = $this->get(route('bank-reconciliation.index', [
            'date_from' => now()->subMonths(2)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
    }

    public function test_match_reconciles_transaction()
    {
        $transaction = BankTransaction::factory()->create(['status' => 'pending']);
        $invoice = Invoice::factory()->create();

        $response = $this->post(route('bank-reconciliation.match', $transaction), [
            'invoice_id' => $invoice->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bank_transactions', [
            'id' => $transaction->id,
            'invoice_id' => $invoice->id,
            'status' => 'reconciled',
        ]);
    }

    public function test_unmatch_undoes_reconciliation()
    {
        $invoice = Invoice::factory()->create();
        $transaction = BankTransaction::factory()->create([
            'status' => 'reconciled',
            'invoice_id' => $invoice->id,
        ]);

        $response = $this->post(route('bank-reconciliation.unmatch', $transaction));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bank_transactions', [
            'id' => $transaction->id,
            'invoice_id' => null,
            'status' => 'pending',
        ]);
    }

    public function test_suggest_returns_view()
    {
        $account = BankAccount::factory()->create();
        BankTransaction::factory()->create([
            'bank_account_id' => $account->id,
            'status' => 'pending',
            'type' => 'credit',
        ]);

        $response = $this->get(route('bank-reconciliation.suggest', $account));

        $response->assertOk();
    }
}
