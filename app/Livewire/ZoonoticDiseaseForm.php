<?php

namespace App\Livewire;

use App\Models\ZoonoticDisease;
use Livewire\Attributes\On;
use Livewire\Component;

class ZoonoticDiseaseForm extends Component
{
    public $zoonoticDiseaseId;
    public $name = '';
    public $category = 'viral';
    public $causative_agent = '';
    public $transmission = '';
    public $animal_symptoms = '';
    public $human_symptoms = '';
    public $incubation_period = '';
    public $prevention = '';
    public $treatment = '';
    public $is_notifiable = false;
    public $species_affected = [];
    public $notes = '';
    public $is_active = true;

    public $speciesOptions = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:viral,bacterial,parasitic,fungal,prion',
        'causative_agent' => 'nullable|string|max:255',
        'transmission' => 'nullable|string',
        'animal_symptoms' => 'nullable|string',
        'human_symptoms' => 'nullable|string',
        'incubation_period' => 'nullable|string|max:100',
        'prevention' => 'nullable|string',
        'treatment' => 'nullable|string',
        'is_notifiable' => 'boolean',
        'species_affected' => 'nullable|array',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->speciesOptions = array_keys(config('species'));
        if ($id) $this->load($id);
    }

    #[On('editZoonoticDisease')]
    public function load($id)
    {
        $this->zoonoticDiseaseId = $id;
        $disease = ZoonoticDisease::findOrFail($id);
        $this->name = $disease->name;
        $this->category = $disease->category;
        $this->causative_agent = $disease->causative_agent ?? '';
        $this->transmission = $disease->transmission ?? '';
        $this->animal_symptoms = $disease->animal_symptoms ?? '';
        $this->human_symptoms = $disease->human_symptoms ?? '';
        $this->incubation_period = $disease->incubation_period ?? '';
        $this->prevention = $disease->prevention ?? '';
        $this->treatment = $disease->treatment ?? '';
        $this->is_notifiable = $disease->is_notifiable;
        $this->species_affected = $disease->species_affected ?? [];
        $this->notes = $disease->notes ?? '';
        $this->is_active = $disease->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->zoonoticDiseaseId = null;
        $this->name = '';
        $this->category = 'viral';
        $this->causative_agent = '';
        $this->transmission = '';
        $this->animal_symptoms = '';
        $this->human_symptoms = '';
        $this->incubation_period = '';
        $this->prevention = '';
        $this->treatment = '';
        $this->is_notifiable = false;
        $this->species_affected = [];
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $nullableFields = ['causative_agent', 'transmission', 'animal_symptoms', 'human_symptoms', 'incubation_period', 'prevention', 'treatment', 'notes'];
        foreach ($nullableFields as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->is_notifiable = (bool) $this->is_notifiable;
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'causative_agent' => $this->causative_agent,
            'transmission' => $this->transmission,
            'animal_symptoms' => $this->animal_symptoms,
            'human_symptoms' => $this->human_symptoms,
            'incubation_period' => $this->incubation_period,
            'prevention' => $this->prevention,
            'treatment' => $this->treatment,
            'is_notifiable' => $this->is_notifiable,
            'species_affected' => $this->species_affected,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->zoonoticDiseaseId) {
            ZoonoticDisease::findOrFail($this->zoonoticDiseaseId)->update($data);
        } else {
            ZoonoticDisease::create($data);
        }

        $this->dispatch('zoonotic-disease-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.zoonotic-disease-form');
    }
}
