<?php

namespace App\Http\Livewire;

use App\Models\DrugFormulary;
use Livewire\Component;

class DosageCalculator extends Component
{
    public $drugs = [];
    public $selectedDrugId;
    public $weightKg;
    public $species;
    public $result = null;
    public $error = null;

    public function mount()
    {
        $this->drugs = DrugFormulary::active()->select('id', 'drug', 'species')->get();
    }

    public function updated($field)
    {
        if ($this->selectedDrugId && $this->weightKg && $this->species) {
            $this->calculate();
        }
    }

    public function calculate()
    {
        $this->error = null;
        $this->result = DrugFormulary::calculateDose($this->selectedDrugId, (float) $this->weightKg, $this->species);

        if (!$this->result) {
            $this->error = 'Nenhuma dosagem encontrada para este fármaco/espécie.';
        }
    }

    public function render()
    {
        $speciesList = DrugFormulary::active()->select('species')->distinct()->pluck('species');
        $drugOptions = $this->species
            ? DrugFormulary::active()->forSpecies($this->species)->get()
            : collect();

        return view('livewire.dosage-calculator', compact('speciesList', 'drugOptions'));
    }
}
