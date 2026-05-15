<?php

namespace Tests\Feature\Controllers;

use App\Models\ControlledSubstance;
use Tests\ModuleTestCase;

class ControlledSubstanceLogControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    private function createSubstance(array $overrides = []): ControlledSubstance
    {
        return ControlledSubstance::create(array_merge([
            'name' => 'Cetamina 10%',
            'active_ingredient' => 'Cetamina',
            'schedule' => 'B1',
            'anvisa_register' => '123456789',
            'unit' => 'ml',
            'current_stock' => 40,
            'min_stock' => 10,
            'is_active' => true,
        ], $overrides));
    }

    public function test_index()
    {
        $response = $this->get(route('controlled-substance-logs.index', ['substance' => 0]));
        $response->assertOk();
    }

    public function test_store()
    {
        $substance = $this->createSubstance();

        $response = $this->post(route('controlled-substance-logs.store', ['substance' => $substance->id]), [
            'controlled_substance_id' => $substance->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'Compra de reposição',
            'notes' => 'Compra',
        ]);

        $response->assertRedirect(route('controlled-substance-logs.index', ['substance' => $substance->id]));
        $this->assertDatabaseHas('controlled_substance_logs', [
            'controlled_substance_id' => $substance->id,
            'type' => 'in',
            'quantity' => 10,
        ]);
    }

    public function test_show()
    {
        $substance = $this->createSubstance();
        $log = $substance->logs()->create([
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => 10,
            'balance_before' => 0,
            'balance_after' => 10,
            'reason' => 'Compra',
        ]);

        $response = $this->get(route('controlled-substance-logs.show', $log));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $substance = $this->createSubstance(['current_stock' => 10]);
        $log = $substance->logs()->create([
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => 10,
            'balance_before' => 0,
            'balance_after' => 10,
            'reason' => 'Compra',
        ]);

        $response = $this->delete(route('controlled-substance-logs.destroy', $log));
        $response->assertRedirect(route('controlled-substance-logs.index', ['substance' => $substance->id]));
        $this->assertDatabaseMissing('controlled_substance_logs', ['id' => $log->id]);
    }
}
