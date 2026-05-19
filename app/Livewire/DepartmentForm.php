<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentForm extends Component
{
    public $departmentId;
    public $name = '';
    public $description = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editDepartment')]
    public function load($id)
    {
        $this->departmentId = $id;
        $department = Department::findOrFail($id);
        $this->name = $department->name;
        $this->description = $department->description ?? '';
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->departmentId = null;
        $this->name = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function save()
    {
        $this->description = $this->description ?: null;
        $this->validate();

        if ($this->departmentId) {
            $department = Department::findOrFail($this->departmentId);
            $department->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        } else {
            Department::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }

        $this->dispatch('department-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.department-form');
    }
}
