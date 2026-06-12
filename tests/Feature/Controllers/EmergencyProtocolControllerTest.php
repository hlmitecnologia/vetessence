<?php

namespace Tests\Feature\Controllers;

use App\Models\EmergencyProtocol;
use Tests\ModuleTestCase;

class EmergencyProtocolControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        EmergencyProtocol::factory()->count(3)->create();

        $response = $this->get(route('emergency-protocols.index'));

        $response->assertOk();
    }

    public function test_index_filters_by_species()
    {
        EmergencyProtocol::factory()->create(['species' => 'Canina']);

        $response = $this->get(route('emergency-protocols.index', ['species' => 'Canina']));

        $response->assertOk();
    }

    public function test_index_filters_by_severity()
    {
        EmergencyProtocol::factory()->create(['severity' => 'critical']);

        $response = $this->get(route('emergency-protocols.index', ['severity' => 'critical']));

        $response->assertOk();
    }

    public function test_index_searches_by_title()
    {
        EmergencyProtocol::factory()->create(['title' => 'Parada Cardíaca']);

        $response = $this->get(route('emergency-protocols.index', ['search' => 'Cardíaca']));

        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('emergency-protocols.store'), [
            'title' => 'Protocolo de Parada Cardíaca',
            'severity' => 'critical',
            'procedure_steps' => '1. Verificar responsividade',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('emergency-protocols.index'));
        $this->assertDatabaseHas('emergency_protocols', ['title' => 'Protocolo de Parada Cardíaca']);
    }

    public function test_show()
    {
        $protocol = EmergencyProtocol::factory()->create();

        $response = $this->get(route('emergency-protocols.show', $protocol));

        $response->assertOk();
    }

    public function test_update()
    {
        $protocol = EmergencyProtocol::factory()->create();

        $response = $this->put(route('emergency-protocols.update', $protocol), [
            'title' => 'Protocolo Atualizado',
            'severity' => 'urgent',
            'procedure_steps' => 'Passos atualizados',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('emergency-protocols.index'));
        $this->assertDatabaseHas('emergency_protocols', [
            'id' => $protocol->id,
            'title' => 'Protocolo Atualizado',
        ]);
    }

    public function test_destroy()
    {
        $protocol = EmergencyProtocol::factory()->create();

        $response = $this->delete(route('emergency-protocols.destroy', $protocol));

        $response->assertRedirect(route('emergency-protocols.index'));
        $this->assertDatabaseMissing('emergency_protocols', ['id' => $protocol->id]);
    }
}
