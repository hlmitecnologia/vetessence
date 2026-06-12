<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\LabEquipmentIntegration;
use App\Models\LabEquipmentResult;
use Tests\ModuleTestCase;

class LabEquipmentControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_receive_creates_result()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/v1/lab-equipment/' . $integration->id . '/receive', [
            'test_type' => 'hematology',
            'raw_data' => ['wbc' => 12.5, 'rbc' => 5.2],
        ]);

        $response->assertCreated();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('lab_equipment_results', [
            'integration_id' => $integration->id,
            'test_type' => 'hematology',
            'status' => 'received',
        ]);
    }

    public function test_receive_fails_when_integration_inactive()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => false]);

        $response = $this->postJson('/api/v1/lab-equipment/' . $integration->id . '/receive', [
            'test_type' => 'hematology',
            'raw_data' => ['wbc' => 12.5],
        ]);

        $response->assertForbidden();
        $response->assertJson(['error' => 'Integration inactive']);
    }

    public function test_receive_validates_required_fields()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/v1/lab-equipment/' . $integration->id . '/receive', []);

        $response->assertJsonValidationErrors(['test_type', 'raw_data']);
    }

    public function test_receive_returns_404_for_nonexistent_integration()
    {
        $response = $this->postJson('/api/v1/lab-equipment/99999/receive', [
            'test_type' => 'hematology',
            'raw_data' => ['wbc' => 12.5],
        ]);

        $response->assertNotFound();
    }

    public function test_receive_accepts_optional_fields()
    {
        $integration = LabEquipmentIntegration::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/v1/lab-equipment/' . $integration->id . '/receive', [
            'result_identifier' => 'RES-001',
            'test_type' => 'biochemistry',
            'raw_data' => ['glucose' => 95],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('lab_equipment_results', [
            'integration_id' => $integration->id,
            'result_identifier' => 'RES-001',
        ]);
    }

    public function test_receive_updates_integration_last_contact_at()
    {
        $integration = LabEquipmentIntegration::factory()->create([
            'is_active' => true,
            'last_contact_at' => null,
        ]);

        $this->postJson('/api/v1/lab-equipment/' . $integration->id . '/receive', [
            'test_type' => 'hematology',
            'raw_data' => ['wbc' => 12.5],
        ]);

        $this->assertNotNull($integration->fresh()->last_contact_at);
    }

    public function test_status_returns_integration_info()
    {
        $integration = LabEquipmentIntegration::factory()->create([
            'is_active' => true,
            'last_contact_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/lab-equipment/' . $integration->id . '/status');

        $response->assertOk();
        $response->assertJson([
            'id' => $integration->id,
            'name' => $integration->name,
            'is_active' => true,
        ]);
    }

    public function test_status_returns_404_for_nonexistent_integration()
    {
        $response = $this->getJson('/api/v1/lab-equipment/99999/status');

        $response->assertNotFound();
    }
}
