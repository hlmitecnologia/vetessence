<?php

namespace Tests\Feature\Livewire;

use App\Models\EmergencyProtocol;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class EmergencyProtocolFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('emergency-protocol-form')
            ->set('title', 'PCR – Parada Cardiorrespiratória')
            ->set('severity', 'critical')
            ->set('procedure_steps', '1. Verificar consciência. 2. Iniciar RCP.')
            ->call('save')
            ->assertDispatched('emergency-protocol-saved');

        $this->assertDatabaseHas('emergency_protocols', ['title' => 'PCR – Parada Cardiorrespiratória']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('emergency-protocol-form')
            ->set('severity', '')
            ->call('save')
            ->assertHasErrors(['title', 'severity', 'procedure_steps']);
    }

    public function test_validates_severity_enum(): void
    {
        Livewire::test('emergency-protocol-form')
            ->set('title', 'Test')
            ->set('severity', 'invalid')
            ->set('procedure_steps', 'Step 1')
            ->call('save')
            ->assertHasErrors(['severity']);
    }

    public function test_can_edit(): void
    {
        $protocol = EmergencyProtocol::factory()->create(['title' => 'Atendimento Inicial']);

        Livewire::test('emergency-protocol-form')
            ->dispatch('editEmergencyProtocol', id: $protocol->id)
            ->assertSet('title', 'Atendimento Inicial')
            ->set('title', 'Atendimento Inicial – Atualizado')
            ->call('save')
            ->assertDispatched('emergency-protocol-saved');

        $this->assertDatabaseHas('emergency_protocols', ['id' => $protocol->id, 'title' => 'Atendimento Inicial – Atualizado']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('emergency-protocol-form')
            ->set('title', 'Temp')
            ->set('procedure_steps', 'Steps')
            ->dispatch('resetForm')
            ->assertSet('title', '')
            ->assertSet('procedure_steps', '')
            ->assertSet('severity', 'stable')
            ->assertSet('is_active', true);
    }
}
