<?php

namespace Tests\Feature\Controllers;

use App\Models\LabEquipmentIntegration;
use Tests\ModuleTestCase;

class LabEquipmentIntegrationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        LabEquipmentIntegration::factory()->count(3)->create();
        $response = $this->get(route('lab-equipment-integrations.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('lab-equipment-integrations.create'));
        $response->assertOk();
    }

    public function test_store_creates_integration()
    {
        $response = $this->post(route('lab-equipment-integrations.store'), [
            'name' => 'Hematology Analyzer',
            'equipment_type' => 'hematology',
            'protocol' => 'rest',
            'endpoint_url' => 'http://localhost:8080/api',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('lab-equipment-integrations.index'));
        $this->assertDatabaseHas('lab_equipment_integrations', [
            'name' => 'Hematology Analyzer',
            'protocol' => 'rest',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('lab-equipment-integrations.store'), []);
        $response->assertSessionHasErrors(['name', 'equipment_type', 'protocol']);
    }

    public function test_show()
    {
        $integration = LabEquipmentIntegration::factory()->create();
        $response = $this->get(route('lab-equipment-integrations.show', $integration));
        $response->assertOk();
    }

    public function test_edit()
    {
        $integration = LabEquipmentIntegration::factory()->create();
        $response = $this->get(route('lab-equipment-integrations.edit', $integration));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $integration = LabEquipmentIntegration::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('lab-equipment-integrations.update', $integration), [
            'name' => 'Updated Analyzer',
            'equipment_type' => 'biochemistry',
            'protocol' => 'hl7',
        ]);

        $response->assertRedirect(route('lab-equipment-integrations.index'));
        $this->assertDatabaseHas('lab_equipment_integrations', [
            'id' => $integration->id,
            'name' => 'Updated Analyzer',
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $integration = LabEquipmentIntegration::factory()->create();

        $response = $this->delete(route('lab-equipment-integrations.destroy', $integration));

        $response->assertRedirect(route('lab-equipment-integrations.index'));
        $this->assertDatabaseMissing('lab_equipment_integrations', ['id' => $integration->id]);
    }
}
