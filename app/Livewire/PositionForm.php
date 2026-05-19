<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Component;

class PositionForm extends Component
{
    public $positionId;
    public $name = '';
    public $description = '';
    public $department_id = '';
    public $departments = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'department_id' => 'nullable|exists:departments,id',
    ];

    public function mount($id = null)
    {
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
        if ($id) $this->load($id);
    }

    #[On('editPosition')]
    public function load($id)
    {
        $this->positionId = $id;
        $position = Position::findOrFail($id);
        $this->name = $position->name;
        $this->description = $position->description ?? '';
        $this->department_id = (string) ($position->department_id ?? '');
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->positionId = null;
        $this->name = '';
        $this->description = '';
        $this->department_id = '';
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
        $this->resetValidation();
    }

    public function save()
    {
        $this->description = $this->description ?: null;
        $this->department_id = $this->department_id ?: null;
        $this->validate();

        if ($this->positionId) {
            $position = Position::findOrFail($this->positionId);
            $position->update([
                'name' => $this->name,
                'description' => $this->description,
                'department_id' => $this->department_id,
            ]);
        } else {
            Position::create([
                'name' => $this->name,
                'description' => $this->description,
                'department_id' => $this->department_id,
            ]);
        }

        $this->dispatch('position-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.position-form');
    }
}
