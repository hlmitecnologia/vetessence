<?php

namespace Tests\Unit\Models;

use App\Models\EmergencyProtocol;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmergencyProtocolTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        EmergencyProtocol::create([
            'title' => 'Protocolo de Emergência',
            'species' => 'Canina',
            'severity' => 'critical',
            'description' => 'Descrição do protocolo',
            'procedure_steps' => 'Passo 1, Passo 2',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('emergency_protocols', [
            'title' => 'Protocolo de Emergência',
            'is_active' => true,
        ]);
    }

    public function test_is_active_cast()
    {
        $protocol = EmergencyProtocol::factory()->create(['is_active' => true]);
        $this->assertTrue($protocol->is_active);
    }

    public function test_active_scope()
    {
        EmergencyProtocol::factory()->create(['is_active' => true]);
        EmergencyProtocol::factory()->create(['is_active' => false]);
        $this->assertEquals(1, EmergencyProtocol::active()->count());
    }

    public function test_for_species_scope()
    {
        EmergencyProtocol::factory()->create(['species' => 'Canina']);
        EmergencyProtocol::factory()->create(['species' => 'Felina']);
        $this->assertEquals(1, EmergencyProtocol::forSpecies('Canina')->count());
    }

    public function test_slug_auto_generated()
    {
        $protocol = EmergencyProtocol::create([
            'title' => 'Protocolo Teste',
            'species' => 'Canina',
            'severity' => 'urgent',
            'procedure_steps' => 'Passo único',
        ]);
        $this->assertEquals('protocolo-teste', $protocol->slug);
    }
}
