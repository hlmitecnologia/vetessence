<?php

namespace Tests\Feature\Livewire;

use App\Models\VaccineProtocol;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class VaccineProtocolFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_protocol()
    {
        Livewire::test('vaccine-protocol-form')
            ->set('species', 'canine')
            ->set('vaccine_name', 'V10')
            ->call('save')
            ->assertDispatched('vaccine-protocol-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('vaccine_protocols', [
            'species' => 'canine',
            'vaccine_name' => 'V10',
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('vaccine-protocol-form')
            ->call('save')
            ->assertHasErrors(['species', 'vaccine_name']);
    }

    public function test_can_edit_existing_protocol()
    {
        $protocol = VaccineProtocol::factory()->create([
            'vaccine_name' => 'V8',
        ]);

        Livewire::test('vaccine-protocol-form', ['id' => $protocol->id])
            ->assertSet('vaccine_name', 'V8')
            ->set('vaccine_name', 'V10')
            ->call('save')
            ->assertDispatched('vaccine-protocol-saved');

        $this->assertDatabaseHas('vaccine_protocols', [
            'id' => $protocol->id,
            'vaccine_name' => 'V10',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $protocol = VaccineProtocol::factory()->create([
            'species' => 'feline',
            'vaccine_name' => 'Antirábica',
        ]);

        Livewire::test('vaccine-protocol-form')
            ->dispatch('editVaccineProtocol', id: $protocol->id)
            ->assertSet('vaccineProtocolId', $protocol->id)
            ->assertSet('vaccine_name', 'Antirábica')
            ->call('save')
            ->assertDispatched('vaccine-protocol-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $protocol = VaccineProtocol::factory()->create();

        Livewire::test('vaccine-protocol-form')
            ->dispatch('editVaccineProtocol', id: $protocol->id)
            ->assertSet('vaccineProtocolId', $protocol->id)
            ->dispatch('resetForm')
            ->assertSet('vaccineProtocolId', null)
            ->assertSet('species', '')
            ->assertSet('vaccine_name', '')
            ->assertSet('is_initial', false)
            ->assertSet('is_core', false)
            ->assertSet('is_active', true);
    }

    public function test_can_create_with_full_details()
    {
        Livewire::test('vaccine-protocol-form')
            ->set('species', 'canine')
            ->set('vaccine_name', 'Giárdia')
            ->set('age_start_weeks', '8')
            ->set('age_end_weeks', '12')
            ->set('is_initial', true)
            ->set('dose_number', '2')
            ->set('booster_interval_months', '12')
            ->set('is_core', false)
            ->call('save')
            ->assertDispatched('vaccine-protocol-saved');

        $this->assertDatabaseHas('vaccine_protocols', [
            'species' => 'canine',
            'vaccine_name' => 'Giárdia',
            'dose_number' => 2,
            'booster_interval_months' => 12,
        ]);
    }

    public function test_can_deactivate_protocol()
    {
        Livewire::test('vaccine-protocol-form')
            ->set('species', 'feline')
            ->set('vaccine_name', 'V4')
            ->set('is_active', false)
            ->call('save')
            ->assertDispatched('vaccine-protocol-saved');

        $this->assertDatabaseHas('vaccine_protocols', [
            'species' => 'feline',
            'vaccine_name' => 'V4',
            'is_active' => false,
        ]);
    }
}
