<?php

namespace Tests\Feature\Livewire;

use App\Models\Hospitalization;
use App\Models\HospitalizationPrescription;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ExecutionBoardTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_render_board()
    {
        $hospitalization = Hospitalization::factory()->create();

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->assertSet('hospitalization.id', $hospitalization->id)
            ->assertSet('date', now()->toDateString());
    }

    public function test_generate_from_prescriptions()
    {
        $hospitalization = Hospitalization::factory()->create();
        $rx = HospitalizationPrescription::create([
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Dipirona',
            'dosage' => '1.0',
            'unit' => 'ml',
            'frequency' => '8/8h',
            'route' => 'IV',
            'start_date' => now()->subDay(),
            'prescribed_by' => User::factory()->create()->id,
        ]);

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->call('generateFromPrescriptions')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('execution_tasks', ['title' => 'Dipirona', 'dosage' => '1.0', 'route' => 'IV']);
    }

    public function test_execute_task()
    {
        $hospitalization = Hospitalization::factory()->create();
        $rx = HospitalizationPrescription::create([
            'hospitalization_id' => $hospitalization->id,
            'medication' => 'Dipirona',
            'dosage' => '1.0',
            'unit' => 'ml',
            'frequency' => '8/8h',
            'route' => 'IV',
            'start_date' => now()->subDay(),
            'prescribed_by' => User::factory()->create()->id,
        ]);

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->call('generateFromPrescriptions');

        $task = $hospitalization->fresh()->executionMaps()->first()->tasks()->first();
        $this->assertNotNull($task, 'No task was generated from prescription');

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->call('execute', $task->id)
            ->assertSet('showExecuteModal', true)
            ->set('executeStatus', 'completed')
            ->set('executeNotes', 'Medicação administrada com sucesso')
            ->call('confirmExecution')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('execution_logs', ['execution_task_id' => $task->id, 'status' => 'completed']);
    }

    public function test_add_manual_task()
    {
        $hospitalization = Hospitalization::factory()->create();

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->call('saveManualTask', 'Curativo', 'procedure', '14:00', 'Trocar curativo a cada 12h');

        $this->assertDatabaseHas('execution_tasks', ['title' => 'Curativo', 'category' => 'procedure', 'source_type' => 'manual']);
    }

    public function test_navigate_days()
    {
        $hospitalization = Hospitalization::factory()->create();

        Livewire::test('execution-board', ['hospitalization' => $hospitalization])
            ->call('nextDay')
            ->assertSet('date', now()->addDay()->toDateString())
            ->call('previousDay')
            ->assertSet('date', now()->toDateString());
    }
}
