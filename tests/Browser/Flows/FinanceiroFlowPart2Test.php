<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class FinanceiroFlowPart2Test extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected User $financeiro;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Teste']);
        $this->financeiro = $this->createUser('financeiro', ['branch_id' => $this->branch->id]);
        $this->admin = $this->createUser('admin', ['branch_id' => $this->branch->id]);
    }

    public function test_nfse_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->financeiro)
                ->visit('/nfse')
                ->waitForText('NFSe Emitidas')
                ->assertSee('NFSe Emitidas');
        });
    }

    public function test_nfe_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->financeiro)
                ->visit('/nfe')
                ->waitForText('NF-e Emitidas')
                ->assertSee('NF-e Emitidas');
        });
    }

    public function test_commission_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/commissions')
                ->waitForText('Comissões')
                ->assertSee('Comissões');
        });
    }

    public function test_bank_reconciliation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/bank-reconciliation')
                ->waitForText('Conciliação Bancária')
                ->assertSee('Conciliação Bancária');
        });
    }

    public function test_services_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/services')
                ->waitForText('Serviços')
                ->assertSee('Serviços');
        });
    }
}
