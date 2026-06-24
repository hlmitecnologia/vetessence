<?php

namespace App\Livewire;

use App\Models\ControlledSubstance;
use Livewire\Attributes\On;
use Livewire\Component;

class ControlledSubstanceForm extends Component
{
    public $controlledSubstanceId;
    public $name = '';
    public $active_ingredient = '';
    public $schedule = '';
    public $anvisa_register = '';
    public $unit = '';
    public $current_stock = '';
    public $min_stock = '';
    public $is_active = true;
    public $notes = '';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'schedule' => 'required|string|max:10',
            'anvisa_register' => 'nullable|string|max:50',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
        if (!$this->controlledSubstanceId) {
            $rules['current_stock'] = 'required|numeric|min:0';
        }
        return $rules;
    }

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editControlledSubstance')]
    public function load($id)
    {
        $this->controlledSubstanceId = $id;
        $s = ControlledSubstance::findOrFail($id);
        $this->name = $s->name;
        $this->active_ingredient = $s->active_ingredient ?? '';
        $this->schedule = $s->schedule;
        $this->anvisa_register = $s->anvisa_register ?? '';
        $this->unit = $s->unit;
        $this->current_stock = (string) $s->current_stock;
        $this->min_stock = (string) ($s->min_stock ?? '');
        $this->is_active = $s->is_active;
        $this->notes = $s->notes ?? '';
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->controlledSubstanceId = null;
        $this->name = '';
        $this->active_ingredient = '';
        $this->schedule = '';
        $this->anvisa_register = '';
        $this->unit = '';
        $this->current_stock = '';
        $this->min_stock = '';
        $this->is_active = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['active_ingredient', 'anvisa_register', 'min_stock', 'notes'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'active_ingredient' => $this->active_ingredient,
            'schedule' => $this->schedule,
            'anvisa_register' => $this->anvisa_register,
            'unit' => $this->unit,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->min_stock,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ];

        if ($this->controlledSubstanceId) {
            unset($data['current_stock']);
            ControlledSubstance::findOrFail($this->controlledSubstanceId)->update($data);
        } else {
            ControlledSubstance::create($data);
        }

        $this->dispatch('controlled-substance-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.controlled-substance-form');
    }
}
