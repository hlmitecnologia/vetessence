<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class FinanceiroFlowPart1Test extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected Tutor $tutor;
    protected Pet $pet;
    protected User $financeiro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Teste']);
        $this->tutor = Tutor::factory()->create(['name' => 'Tutor Teste']);
        $this->pet = Pet::factory()->create(['name' => 'Rex']);
        $this->tutor->pets()->attach($this->pet->id);
        $this->financeiro = $this->createUser('financeiro', ['branch_id' => $this->branch->id]);
    }

    public function test_invoice_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->financeiro)
                ->visit('/invoices')
                ->waitForText('Faturas')
                ->assertSee('Faturas');
        });
    }

    public function test_invoice_show(): void
    {
        $invoice = Invoice::factory()->create([
            'branch_id' => $this->branch->id,
            'tutor_id' => $this->tutor->id,
        ]);

        $this->browse(function (Browser $browser) use ($invoice) {
            $browser->loginAs($this->financeiro)
                ->visit('/invoices/' . $invoice->id)
                ->waitForText('Fatura')
                ->assertSee('Fatura');
        });
    }

    public function test_payment_gateway_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->financeiro)
                ->visit('/payment-gateways')
                ->waitForText('Gateways de Pagamento')
                ->assertSee('Gateways de Pagamento');
        });
    }

    public function test_invoice_pay_charge_flow(): void
    {
        $invoice = Invoice::factory()->create([
            'branch_id' => $this->branch->id,
            'tutor_id' => $this->tutor->id,
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($invoice) {
            $browser->loginAs($this->financeiro)
                ->visit('/invoices/' . $invoice->id)
                ->waitForText('Fatura')
                ->assertSee($invoice->invoice_number);
        });
    }

    public function test_pdv_portal_pagamento(): void
    {
        $invoice = Invoice::factory()->create([
            'branch_id' => $this->branch->id,
            'tutor_id' => $this->tutor->id,
        ]);

        $this->browse(function (Browser $browser) use ($invoice) {
            $browser->loginAs($this->financeiro)
                ->visit('/invoices/' . $invoice->id)
                ->waitForText('Fatura')
                ->assertSee($invoice->invoice_number);
        });
    }
}
