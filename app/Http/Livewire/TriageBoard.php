<?php

namespace App\Http\Livewire;

use App\Models\TriageRecord;
use Livewire\Component;
use Livewire\WithPagination;

class TriageBoard extends Component
{
    use WithPagination;

    public $newRedIds = [];
    public $previousRedIds = [];

    protected $listeners = ['refreshBoard' => '$refresh'];

    public function mount()
    {
        $this->previousRedIds = $this->getRedIds();
    }

    public function getRedIds()
    {
        return TriageRecord::whereIn('status', ['waiting', 'in_consultation'])
            ->where('severity', 'red')
            ->pluck('id')
            ->toArray();
    }

    public function markAsInConsultation($id)
    {
        $record = TriageRecord::findOrFail($id);
        $record->update(['status' => 'in_consultation']);
    }

    public function markAsSeen($id)
    {
        $record = TriageRecord::findOrFail($id);
        $record->update([
            'status' => 'seen',
            'seen_at' => now(),
            'triage_vet_id' => auth()->id(),
        ]);
    }

    public function markAsDischarged($id)
    {
        $record = TriageRecord::findOrFail($id);
        $record->update([
            'status' => 'discharged',
            'discharged_at' => now(),
        ]);
    }

    public function render()
    {
        $currentRedIds = $this->getRedIds();
        $newRed = array_diff($currentRedIds, $this->previousRedIds);
        if (!empty($newRed)) {
            $this->dispatchBrowserEvent('new-red-triage', ['ids' => array_values($newRed)]);
        }
        $this->previousRedIds = $currentRedIds;

        $waiting = TriageRecord::with(['pet', 'assignedVet'])
            ->whereIn('status', ['waiting', 'in_consultation'])
            ->orderByRaw("CASE severity WHEN 'red' THEN 4 WHEN 'orange' THEN 3 WHEN 'yellow' THEN 2 ELSE 1 END DESC")
            ->orderBy('check_in_at')
            ->get();

        $history = TriageRecord::with(['pet'])
            ->whereIn('status', ['seen', 'discharged'])
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.triage-board', compact('waiting', 'history'));
    }
}
