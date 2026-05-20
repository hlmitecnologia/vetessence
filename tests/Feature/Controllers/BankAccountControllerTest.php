<?php

namespace Tests\Feature\Controllers;

use App\Models\BankAccount;
use App\Models\Branch;
use Tests\ModuleTestCase;

class BankAccountControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        BankAccount::factory()->create();
        $response = $this->get(route('bank-accounts.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('bank-accounts.create'));
        $response->assertOk();
    }

    public function test_store_creates_account()
    {
        $branch = Branch::factory()->create();
        $response = $this->post(route('bank-accounts.store'), [
            'bank' => 'Banco do Brasil',
            'agency' => '1234',
            'account' => '56789-0',
            'account_type' => 'checking',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('bank_accounts', ['bank' => 'Banco do Brasil']);
    }

    public function test_show()
    {
        $account = BankAccount::factory()->create();
        $response = $this->get(route('bank-accounts.show', $account));
        $response->assertOk();
    }

    public function test_edit()
    {
        $account = BankAccount::factory()->create();
        $response = $this->get(route('bank-accounts.edit', $account));
        $response->assertOk();
    }

    public function test_update()
    {
        $account = BankAccount::factory()->create();
        $response = $this->put(route('bank-accounts.update', $account), [
            'bank' => 'Itaú',
            'agency' => '4321',
            'account' => '98765-0',
            'account_type' => 'savings',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('bank_accounts', ['id' => $account->id, 'bank' => 'Itaú']);
    }

    public function test_destroy()
    {
        $account = BankAccount::factory()->create();
        $response = $this->delete(route('bank-accounts.destroy', $account));
        $response->assertRedirect();
        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }
}
