<?php

namespace App\Livewire;

use App\Models\DrugFormulary;
use Livewire\Attributes\On;
use Livewire\Component;

class DrugFormularyForm extends Component
{
    public $drugFormularyId;
    public $drug = '';
    public $species = '';
    public $dosage_mg_kg = '';
    public $max_dose = '';
    public $route = '';
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'drug' => 'required|string|max:150',
        'species' => 'required|string|max:50',
        'dosage_mg_kg' => 'required|numeric|min:0.01',
        'max_dose' => 'nullable|numeric|min:0',
        'route' => 'nullable|string|max:50',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editDrugFormulary')]
    public function load($id)
    {
        $this->drugFormularyId = $id;
        $f = DrugFormulary::findOrFail($id);
        $this->drug = $f->drug;
        $this->species = $f->species;
        $this->dosage_mg_kg = (string) $f->dosage_mg_kg;
        $this->max_dose = (string) ($f->max_dose ?? '');
        $this->route = $f->route ?? '';
        $this->notes = $f->notes ?? '';
        $this->is_active = $f->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->drugFormularyId = null;
        $this->drug = '';
        $this->species = '';
        $this->dosage_mg_kg = '';
        $this->max_dose = '';
        $this->route = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['max_dose', 'route', 'notes'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'drug' => $this->drug,
            'species' => $this->species,
            'dosage_mg_kg' => $this->dosage_mg_kg,
            'max_dose' => $this->max_dose,
            'route' => $this->route,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->drugFormularyId) {
            DrugFormulary::findOrFail($this->drugFormularyId)->update($data);
        } else {
            DrugFormulary::create($data);
        }

        $this->dispatch('drug-formulary-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.drug-formulary-form');
    }
}
