<?php

namespace App\Livewire;

use App\Models\BreedDefault;
use Livewire\Attributes\On;
use Livewire\Component;

class BreedDefaultForm extends Component
{
    public $breedDefaultId;
    public $species = '';
    public $breed = '';
    public $size = '';
    public $avg_weight_min = '';
    public $avg_weight_max = '';
    public $avg_lifespan_min = '';
    public $avg_lifespan_max = '';
    public $temperament = '';
    public $predispositions = '';
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'species' => 'required|string|max:50',
        'breed' => 'required|string|max:100',
        'size' => 'nullable|string|max:20',
        'avg_weight_min' => 'nullable|numeric|min:0',
        'avg_weight_max' => 'nullable|numeric|min:0',
        'avg_lifespan_min' => 'nullable|integer|min:0',
        'avg_lifespan_max' => 'nullable|integer|min:0',
        'temperament' => 'nullable|string|max:255',
        'predispositions' => 'nullable|string',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editBreedDefault')]
    public function load($id)
    {
        $this->breedDefaultId = $id;
        $default = BreedDefault::findOrFail($id);
        $this->species = $default->species;
        $this->breed = $default->breed;
        $this->size = $default->size ?? '';
        $this->avg_weight_min = (string) ($default->avg_weight_min ?? '');
        $this->avg_weight_max = (string) ($default->avg_weight_max ?? '');
        $this->avg_lifespan_min = (string) ($default->avg_lifespan_min ?? '');
        $this->avg_lifespan_max = (string) ($default->avg_lifespan_max ?? '');
        $this->temperament = $default->temperament ?? '';
        $this->predispositions = $default->predispositions ?? '';
        $this->notes = $default->notes ?? '';
        $this->is_active = $default->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->breedDefaultId = null;
        $this->species = '';
        $this->breed = '';
        $this->size = '';
        $this->avg_weight_min = '';
        $this->avg_weight_max = '';
        $this->avg_lifespan_min = '';
        $this->avg_lifespan_max = '';
        $this->temperament = '';
        $this->predispositions = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['size', 'temperament', 'predispositions', 'notes'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        foreach (['avg_weight_min', 'avg_weight_max', 'avg_lifespan_min', 'avg_lifespan_max'] as $f) {
            $this->$f = $this->$f !== '' ? $this->$f : null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'species' => $this->species,
            'breed' => $this->breed,
            'size' => $this->size,
            'avg_weight_min' => $this->avg_weight_min,
            'avg_weight_max' => $this->avg_weight_max,
            'avg_lifespan_min' => $this->avg_lifespan_min,
            'avg_lifespan_max' => $this->avg_lifespan_max,
            'temperament' => $this->temperament,
            'predispositions' => $this->predispositions,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->breedDefaultId) {
            BreedDefault::findOrFail($this->breedDefaultId)->update($data);
        } else {
            BreedDefault::create($data);
        }

        $this->dispatch('breed-default-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.breed-default-form');
    }
}
