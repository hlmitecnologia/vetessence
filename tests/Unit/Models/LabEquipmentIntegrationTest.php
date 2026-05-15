<?php

namespace Tests\Unit\Models;

use App\Models\LabEquipmentIntegration;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LabEquipmentIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        LabEquipmentIntegration::create([
            'name' => 'Analisador Hematologico',
            'equipment_type' => 'hematology',
            'protocol' => 'HL7',
            'endpoint_url' => 'http://192.168.1.100/api',
            'api_key' => 'api_key_123',
            'ip_address' => '192.168.1.100',
            'port' => 8080,
            'is_active' => true,
            'config' => ['timeout' => 30],
            'notes' => 'Equipamento do laboratorio',
            'branch_id' => null,
            'last_contact_at' => now(),
        ]);

        $this->assertDatabaseHas('lab_equipment_integrations', [
            'name' => 'Analisador Hematologico',
            'equipment_type' => 'hematology',
            'is_active' => true,
        ]);
    }

    public function test_casts()
    {
        $integration = LabEquipmentIntegration::create([
            'name' => 'Teste',
            'equipment_type' => 'biochemistry',
            'is_active' => true,
            'config' => ['param' => 'value'],
            'last_contact_at' => now(),
        ]);

        $this->assertIsBool($integration->is_active);
        $this->assertIsArray($integration->config);
        $this->assertInstanceOf(\Carbon\Carbon::class, $integration->last_contact_at);
    }
}
