<?php

namespace App\Livewire;

use App\Models\ExecutionMap;
use App\Models\ExecutionTask;
use App\Models\ExecutionLog;
use App\Models\Hospitalization;
use Livewire\Component;

class ExecutionBoard extends Component
{
    public Hospitalization $hospitalization;
    public $date;
    public $executionMap;
    public $tasks = [];
    public $showExecuteModal = false;
    public $executingTaskId;
    public $executeStatus = 'completed';
    public $executeNotes = '';

    protected $rules = [
        'executeStatus' => 'required|in:completed,skipped,partially',
        'executeNotes' => 'nullable|string|max:500',
    ];

    public function mount(Hospitalization $hospitalization, $date = null)
    {
        $this->hospitalization = $hospitalization;
        $this->date = $date ?? now()->toDateString();
        $this->loadMap();
    }

    public function loadMap()
    {
        $this->executionMap = ExecutionMap::firstOrCreate(
            [
                'hospitalization_id' => $this->hospitalization->id,
                'date' => $this->date,
            ],
            [
                'created_by' => auth()->id(),
            ]
        );
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $this->tasks = $this->executionMap
            ->tasks()
            ->orderBy('scheduled_time')
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function previousDay()
    {
        $this->date = now()->parse($this->date)->subDay()->toDateString();
        $this->loadMap();
    }

    public function nextDay()
    {
        $this->date = now()->parse($this->date)->addDay()->toDateString();
        $this->loadMap();
    }

    public function generateFromPrescriptions()
    {
        $prescriptions = $this->hospitalization->prescriptions()
            ->whereDate('start_date', '<=', $this->date)
            ->get();

        $count = 0;
        foreach ($prescriptions as $rx) {
            if ($rx->end_date && now()->parse($this->date)->gt(now()->parse($rx->end_date))) continue;

            $hours = ExecutionTask::parseFrequency($rx->frequency ?? 'every_24h');

            foreach ($hours as $hour) {
                $exists = $this->executionMap->tasks()
                    ->where('source_type', 'hospitalization_prescription')
                    ->where('source_id', $rx->id)
                    ->where('scheduled_time', sprintf('%02d:00', $hour))
                    ->exists();
                if ($exists) continue;

                $this->executionMap->tasks()->create([
                    'category' => 'medication',
                    'title' => $rx->medication,
                    'description' => $rx->notes,
                    'scheduled_time' => sprintf('%02d:00', $hour),
                    'frequency' => $rx->frequency,
                    'route' => $rx->route,
                    'dosage' => $rx->dosage,
                    'unit' => $rx->unit,
                    'source_type' => 'hospitalization_prescription',
                    'source_id' => $rx->id,
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                ]);
                $count++;
            }
        }

        $this->loadTasks();
        $this->dispatch('notify', type: 'success', message: "{$count} tarefa(s) gerada(s) com sucesso.");
    }

    public function execute($taskId)
    {
        $this->executingTaskId = $taskId;
        $this->executeStatus = 'completed';
        $this->executeNotes = '';
        $this->showExecuteModal = true;
    }

    public function confirmExecution()
    {
        $this->validate();

        $task = ExecutionTask::findOrFail($this->executingTaskId);

        ExecutionLog::create([
            'execution_task_id' => $task->id,
            'performed_at' => now(),
            'performed_by' => auth()->id(),
            'status' => $this->executeStatus,
            'notes' => $this->executeNotes ?: null,
        ]);

        $task->update([
            'status' => $this->executeStatus,
            'observations' => $this->executeNotes ?: null,
        ]);

        $this->showExecuteModal = false;
        $this->loadTasks();
        $this->dispatch('notify', type: 'success', message: 'Execução registrada.');
    }

    public function addManualTask()
    {
        $this->dispatch('open-manual-task-modal');
    }

    public function saveManualTask($title, $category = 'procedure', $scheduledTime = null, $description = null)
    {
        $this->executionMap->tasks()->create([
            'category' => $category,
            'title' => $title,
            'description' => $description,
            'scheduled_time' => $scheduledTime ?: null,
            'source_type' => 'manual',
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        $this->loadTasks();
        $this->dispatch('notify', type: 'success', message: 'Tarefa manual adicionada.');
    }

    public function getGroupedTasksProperty()
    {
        $groups = ['morning' => [], 'afternoon' => [], 'night' => []];
        foreach ($this->tasks as $task) {
            $time = $task['scheduled_time'] ? substr($task['scheduled_time'], 0, 2) : '';
            $hour = (int) $time;
            if ($hour >= 6 && $hour < 12) $groups['morning'][] = $task;
            elseif ($hour >= 12 && $hour < 18) $groups['afternoon'][] = $task;
            else $groups['night'][] = $task;
        }
        return $groups;
    }

    public function getOverdueTasksProperty()
    {
        return array_filter($this->tasks, function ($task) {
            if (!in_array($task['status'], ['pending', 'in_progress'])) return false;
            if (!$task['scheduled_time']) return false;
            return $task['scheduled_time'] < now()->format('H:i:s');
        });
    }

    public function render()
    {
        return view('livewire.execution-board');
    }
}
