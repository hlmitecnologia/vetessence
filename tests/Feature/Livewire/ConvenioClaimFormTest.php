<?php

namespace Tests\Feature\Livewire;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Invoice;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ConvenioClaimFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_claim()
    {
        $convenioPet = ConvenioPet::factory()->create();
        $invoice = Invoice::factory()->create();

        Livewire::test('convenio-claim-form')
            ->set('convenio_pet_id', (string) $convenioPet->id)
            ->set('invoice_id', (string) $invoice->id)
            ->set('amount_requested', '150.00')
            ->call('save')
            ->assertDispatched('convenio-claim-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('convenio_claims', [
            'convenio_pet_id' => $convenioPet->id,
            'amount_requested' => 150.00,
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('convenio-claim-form')
            ->call('save')
            ->assertHasErrors(['convenio_pet_id', 'amount_requested']);
    }

    public function test_can_edit_existing_claim()
    {
        $claim = ConvenioClaim::factory()->create([
            'amount_requested' => 100.00,
        ]);

        Livewire::test('convenio-claim-form', ['id' => $claim->id])
            ->assertSet('convenioClaimId', $claim->id)
            ->set('amount_requested', '200.00')
            ->call('save')
            ->assertDispatched('convenio-claim-saved');

        $this->assertDatabaseHas('convenio_claims', [
            'id' => $claim->id,
            'amount_requested' => 200.00,
        ]);
    }

    public function test_can_edit_via_event()
    {
        $claim = ConvenioClaim::factory()->create([
            'amount_requested' => 300.00,
        ]);

        Livewire::test('convenio-claim-form')
            ->dispatch('editConvenioClaim', id: $claim->id)
            ->assertSet('convenioClaimId', $claim->id)
            ->set('amount_requested', '350.00')
            ->call('save')
            ->assertDispatched('convenio-claim-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $claim = ConvenioClaim::factory()->create();

        Livewire::test('convenio-claim-form')
            ->dispatch('editConvenioClaim', id: $claim->id)
            ->assertSet('convenioClaimId', $claim->id)
            ->dispatch('resetForm')
            ->assertSet('convenioClaimId', null)
            ->assertSet('convenio_pet_id', '')
            ->assertSet('amount_requested', '');
    }

    public function test_can_create_with_notes()
    {
        $convenioPet = ConvenioPet::factory()->create();

        Livewire::test('convenio-claim-form')
            ->set('convenio_pet_id', (string) $convenioPet->id)
            ->set('amount_requested', '250.00')
            ->set('notes', 'Observação do pedido')
            ->call('save')
            ->assertDispatched('convenio-claim-saved');

        $this->assertDatabaseHas('convenio_claims', [
            'amount_requested' => 250.00,
            'notes' => 'Observação do pedido',
        ]);
    }
}
