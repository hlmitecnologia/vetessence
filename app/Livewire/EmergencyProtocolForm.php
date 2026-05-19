<?php

namespace App\Livewire;

use App\Models\EmergencyProtocol;
use Livewire\Attributes\On;
use Livewire\Component;

class EmergencyProtocolForm extends Component
{
    public $emergencyProtocolId;
    public $title = '';
    public $species = '';
    public $severity = 'stable';
    public $description = '';
    public $procedure_steps = '';
    public $medications = '';
    public $category = '';
    public $is_active = true;

    protected $rules = [
        'title' => 'required|string|max:200',
        'species' => 'nullable|string|max:50',
        'severity' => 'required|in:critical,urgent,stable',
        'description' => 'nullable|string',
        'procedure_steps' => 'required|string',
        'medications' => 'nullable|string|max:500',
        'category' => 'nullable|string|max:100',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editEmergencyProtocol')]
    public function load($id)
    {
        $this->emergencyProtocolId = $id;
        $p = EmergencyProtocol::findOrFail($id);
        $this->title = $p->title;
        $this->species = $p->species ?? '';
        $this->severity = $p->severity;
        $this->description = $p->description ?? '';
        $this->procedure_steps = $p->procedure_steps;
        $this->medications = $p->medications ?? '';
        $this->category = $p->category ?? '';
        $this->is_active = $p->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->emergencyProtocolId = null;
        $this->title = '';
        $this->species = '';
        $this->severity = 'stable';
        $this->description = '';
        $this->procedure_steps = '';
        $this->medications = '';
        $this->category = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['species', 'description', 'medications', 'category'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'title' => $this->title,
            'species' => $this->species,
            'severity' => $this->severity,
            'description' => $this->description,
            'procedure_steps' => $this->procedure_steps,
            'medications' => $this->medications,
            'category' => $this->category,
            'is_active' => $this->is_active,
        ];

        if ($this->emergencyProtocolId) {
            EmergencyProtocol::findOrFail($this->emergencyProtocolId)->update($data);
        } else {
            EmergencyProtocol::create($data);
        }

        $this->dispatch('emergency-protocol-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.emergency-protocol-form');
    }
}
