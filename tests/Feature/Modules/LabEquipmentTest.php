<?php

namespace Tests\Feature\Modules;

use App\Models\LabEquipmentIntegration;
use App\Models\LabEquipmentResult;
use Tests\ModuleTestCase;

class LabEquipmentTest extends ModuleTestCase
{
    public function test_index()
    {
        $this->loginAs('admin');
        $response = $this->get(route('lab-equipment-integrations.index'));
        $response->assertOk();
    }

    public function test_store_creates_integration()
    {
        $this->loginAs('admin');
        $response = $this->post(route('lab-equipment-integrations.store'), [
            'name' => 'Analisador Hematológico ABC',
            'equipment_type' => 'hematology',
            'protocol' => 'rest',
            'endpoint_url' => 'https://lab.example.com/api',
            'ip_address' => '192.168.1.100',
            'port' => 8080,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('lab-equipment-integrations.index'));
        $this->assertDatabaseHas('lab_equipment_integrations', ['name' => 'Analisador Hematológico ABC']);
    }

    public function test_api_receive_result()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => true]);
        $response = $this->postJson("/api/v1/lab-equipment/{$integration->id}/receive", [
            'test_type' => 'hemograma',
            'raw_data' => ['wbc' => 12.5, 'rbc' => 7.2],
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('lab_equipment_results', ['test_type' => 'hemograma']);
    }

    public function test_api_reject_inactive_integration()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => false]);
        $response = $this->postJson("/api/v1/lab-equipment/{$integration->id}/receive", [
            'test_type' => 'hemograma',
            'raw_data' => [],
        ]);
        $response->assertForbidden();
    }
}
