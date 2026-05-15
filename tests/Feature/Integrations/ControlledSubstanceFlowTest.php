<?php

namespace Tests\Feature\Integrations;

use App\Models\Branch;
use App\Models\ControlledSubstance;
use Tests\ModuleTestCase;

class ControlledSubstanceFlowTest extends ModuleTestCase
{
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
    }

    public function test_controlled_substance_stock_flow()
    {
        $createResponse = $this->post(route('controlled-substances.store'), [
            'name' => 'Cetamina 10%',
            'active_ingredient' => 'Cetamina',
            'schedule' => 'B1',
            'anvisa_register' => '123456789',
            'unit' => 'ml',
            'current_stock' => 0,
            'min_stock' => 10,
            'is_active' => true,
            'notes' => 'Substância controlada para teste',
            'branch_id' => $this->branch->id,
        ]);
        $createResponse->assertSessionDoesntHaveErrors();
        $createResponse->assertRedirect();

        $this->assertDatabaseHas('controlled_substances', [
            'name' => 'Cetamina 10%',
            'current_stock' => 0,
        ]);
        $substance = ControlledSubstance::where('name', 'Cetamina 10%')->first();

        $inResponse = $this->post(
            route('controlled-substance-logs.store', ['substance' => $substance->id]),
            [
                'controlled_substance_id' => $substance->id,
                'type' => 'in',
                'quantity' => 100,
                'reason' => 'Compra de reposição',
                'notes' => 'Nota fiscal 12345',
                'branch_id' => $this->branch->id,
            ]
        );
        $inResponse->assertSessionDoesntHaveErrors();
        $inResponse->assertRedirect();

        $this->assertDatabaseHas('controlled_substance_logs', [
            'controlled_substance_id' => $substance->id,
            'type' => 'in',
            'quantity' => 100,
        ]);

        $substance->current_stock += 100;
        $substance->save();

        $outResponse = $this->post(
            route('controlled-substance-logs.store', ['substance' => $substance->id]),
            [
                'controlled_substance_id' => $substance->id,
                'type' => 'out',
                'quantity' => 30,
                'reason' => 'Uso em procedimento cirúrgico',
                'notes' => 'Paciente: Rex',
                'branch_id' => $this->branch->id,
            ]
        );
        $outResponse->assertSessionDoesntHaveErrors();
        $outResponse->assertRedirect();

        $this->assertDatabaseHas('controlled_substance_logs', [
            'controlled_substance_id' => $substance->id,
            'type' => 'out',
            'quantity' => 30,
        ]);

        $substance->current_stock -= 30;
        $substance->save();

        $this->assertEquals(70, $substance->fresh()->current_stock);
    }
}
