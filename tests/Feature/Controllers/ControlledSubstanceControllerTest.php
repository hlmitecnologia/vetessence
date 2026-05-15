<?php

namespace Tests\Feature\Controllers;

use App\Models\ControlledSubstance;
use Tests\ModuleTestCase;

class ControlledSubstanceControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('controlled-substances.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('controlled-substances.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('controlled-substances.store'), [
            'name' => 'Cetamina 10%',
            'active_ingredient' => 'Cetamina',
            'schedule' => 'B1',
            'anvisa_register' => '123456789',
            'unit' => 'ml',
            'current_stock' => 100,
            'min_stock' => 10,
            'is_active' => true,
            'notes' => 'Teste',
        ]);

        $response->assertRedirect(route('controlled-substances.index'));
        $this->assertDatabaseHas('controlled_substances', ['name' => 'Cetamina 10%']);
    }

    private function createSubstance(array $overrides = []): ControlledSubstance
    {
        return ControlledSubstance::create(array_merge([
            'name' => 'Cetamina 10%',
            'active_ingredient' => 'Cetamina',
            'schedule' => 'B1',
            'anvisa_register' => '123456789',
            'unit' => 'ml',
            'current_stock' => 100,
            'min_stock' => 10,
            'is_active' => true,
        ], $overrides));
    }

    public function test_show()
    {
        $substance = $this->createSubstance();

        $response = $this->get(route('controlled-substances.show', $substance));
        $response->assertOk();
    }

    public function test_edit()
    {
        $substance = $this->createSubstance();

        $response = $this->get(route('controlled-substances.edit', $substance));
        $response->assertOk();
    }

    public function test_update()
    {
        $substance = $this->createSubstance();

        $response = $this->put(route('controlled-substances.update', $substance), [
            'name' => 'Cetamina Atualizada',
            'active_ingredient' => 'Cetamina',
            'schedule' => 'B1',
            'anvisa_register' => '123456789',
            'unit' => 'ml',
            'current_stock' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('controlled-substances.index'));
        $this->assertDatabaseHas('controlled_substances', ['name' => 'Cetamina Atualizada']);
    }

    public function test_destroy()
    {
        $substance = $this->createSubstance();

        $response = $this->delete(route('controlled-substances.destroy', $substance));
        $response->assertRedirect(route('controlled-substances.index'));
        $this->assertDatabaseMissing('controlled_substances', ['id' => $substance->id]);
    }

    public function test_report_monthly()
    {
        $response = $this->get(route('controlled-substances.reports.monthly'));
        $response->assertOk();
    }

    public function test_report_annual()
    {
        $response = $this->get(route('controlled-substances.reports.annual'));
        $response->assertOk();
    }
}
