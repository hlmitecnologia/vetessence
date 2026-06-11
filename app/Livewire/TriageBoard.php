<?php

namespace App\Livewire;

use App\Models\TriageRecord;
use Livewire\Component;

class TriageBoard extends Component
{
    public $previousRedIds = [];

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

    public function updateStatus($caseId, $newStatus)
    {
        $validStatuses = ['waiting', 'in_consultation', 'seen', 'discharged'];
        if (!in_array($newStatus, $validStatuses)) {
            return;
        }

        $record = TriageRecord::findOrFail($caseId);
        $update = ['status' => $newStatus];

        if ($newStatus === 'seen' && !$record->seen_at) {
            $update['seen_at'] = now();
            $update['triage_vet_id'] = auth()->id();
        }
        if ($newStatus === 'discharged') {
            $update['discharged_at'] = now();
        }

        $record->update($update);
    }

    public function render()
    {
        $currentRedIds = $this->getRedIds();
        $newRed = array_diff($currentRedIds, $this->previousRedIds);
        if (!empty($newRed)) {
            $this->dispatch('new-red-triage', ids: array_values($newRed));
        }
        $this->previousRedIds = $currentRedIds;

        $orderRaw = "CASE severity WHEN 'red' THEN 4 WHEN 'orange' THEN 3 WHEN 'yellow' THEN 2 ELSE 1 END DESC";

        $triageCases = TriageRecord::with(['pet.tutors', 'assignedVet'])
            ->orderByRaw($orderRaw)
            ->orderBy('check_in_at')
            ->get()
            ->groupBy('status');

        return view('livewire.triage-board', compact('triageCases', 'newRed'));
    }
}
