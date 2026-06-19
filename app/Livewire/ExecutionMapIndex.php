<?php

namespace App\Livewire;

use App\Models\Hospitalization;
use Livewire\Component;
use Livewire\WithPagination;

class ExecutionMapIndex extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search = '';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getHospitalizationsProperty()
    {
        $query = Hospitalization::with(['pet', 'tutor', 'executionMaps'])
            ->withCount(['executionMaps as pending_tasks_count' => function ($q) {
                $q->whereHas('tasks', function ($q) {
                    $q->where('status', 'pending');
                });
            }]);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('pet', fn ($q) => $q->where('name', 'like', $term))
                    ->orWhereHas('tutor', fn ($q) => $q->where('name', 'like', $term));
            });
        }

        return $query->orderByRaw("FIELD(status, 'discharged', 'transferred', 'active', 'admitted') DESC")
            ->orderBy('admission_date', 'desc')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.execution-map-index', [
            'hospitalizations' => $this->hospitalizations,
        ]);
    }
}
