<?php

namespace App\Livewire;

use App\Models\DrugInteraction;
use Livewire\Attributes\On;
use Livewire\Component;

class DrugInteractionForm extends Component
{
    public $drugInteractionId;
    public $drug_a = '';
    public $drug_b = '';
    public $severity = 'caution';
    public $description = '';
    public $mechanism = '';
    public $management = '';
    public $source = '';
    public $category = '';
    public $is_active = true;

    protected $rules = [
        'drug_a' => 'required|string|max:255',
        'drug_b' => 'required|string|max:255|different:drug_a',
        'severity' => 'required|in:contraindicated,caution,minor',
        'description' => 'required|string',
        'mechanism' => 'nullable|string|max:255',
        'management' => 'nullable|string',
        'source' => 'nullable|string|max:100',
        'category' => 'nullable|string|max:100',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editDrugInteraction')]
    public function load($id)
    {
        $this->drugInteractionId = $id;
        $interaction = DrugInteraction::findOrFail($id);
        $this->drug_a = $interaction->drug_a;
        $this->drug_b = $interaction->drug_b;
        $this->severity = $interaction->severity;
        $this->description = $interaction->description;
        $this->mechanism = $interaction->mechanism ?? '';
        $this->management = $interaction->management ?? '';
        $this->source = $interaction->source ?? '';
        $this->category = $interaction->category ?? '';
        $this->is_active = $interaction->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->drugInteractionId = null;
        $this->drug_a = '';
        $this->drug_b = '';
        $this->severity = 'caution';
        $this->description = '';
        $this->mechanism = '';
        $this->management = '';
        $this->source = '';
        $this->category = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['mechanism', 'management', 'source', 'category'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->drug_a = trim($this->drug_a);
        $this->drug_b = trim($this->drug_b);
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'drug_a' => $this->drug_a,
            'drug_b' => $this->drug_b,
            'severity' => $this->severity,
            'description' => $this->description,
            'mechanism' => $this->mechanism,
            'management' => $this->management,
            'source' => $this->source,
            'category' => $this->category,
            'is_active' => $this->is_active,
        ];

        if ($this->drugInteractionId) {
            $existing = DrugInteraction::where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('drug_a', $this->drug_a)->where('drug_b', $this->drug_b);
                })->orWhere(function ($q2) {
                    $q2->where('drug_a', $this->drug_b)->where('drug_b', $this->drug_a);
                });
            })->where('id', '!=', $this->drugInteractionId)->exists();

            if ($existing) {
                session()->flash('error', 'Esta interação já está cadastrada.');
                return;
            }

            DrugInteraction::findOrFail($this->drugInteractionId)->update($data);
        } else {
            $existing = DrugInteraction::where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('drug_a', $this->drug_a)->where('drug_b', $this->drug_b);
                })->orWhere(function ($q2) {
                    $q2->where('drug_a', $this->drug_b)->where('drug_b', $this->drug_a);
                });
            })->exists();

            if ($existing) {
                session()->flash('error', 'Esta interação já está cadastrada.');
                return;
            }

            DrugInteraction::create($data);
        }

        $this->dispatch('drug-interaction-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.drug-interaction-form');
    }
}
