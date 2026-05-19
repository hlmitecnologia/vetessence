<?php

namespace App\Livewire;

use App\Models\PetDeathRecord;
use App\Models\Pet;
use Livewire\Attributes\On;
use Livewire\Component;

class PetDeathRecordForm extends Component
{
    public $petDeathRecordId;
    public $pet_id = '';
    public $death_date = '';
    public $cause = '';
    public $attending_vet = '';
    public $notes = '';
    public $disposition = '';

    public $pets = [];

    protected $rules = [
        'pet_id' => 'required|exists:pets,id',
        'death_date' => 'required|date',
        'cause' => 'nullable|string|max:255',
        'attending_vet' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'disposition' => 'nullable|string|max:50',
    ];

    public function mount($id = null)
    {
        $this->pets = Pet::orderBy('name')->get();
        if ($id) $this->load($id);
    }

    #[On('editPetDeathRecord')]
    public function load($id)
    {
        $this->petDeathRecordId = $id;
        $r = PetDeathRecord::findOrFail($id);
        $this->pet_id = (string) $r->pet_id;
        $this->death_date = $r->death_date->format('Y-m-d');
        $this->cause = $r->cause ?? '';
        $this->attending_vet = $r->attending_vet ?? '';
        $this->notes = $r->notes ?? '';
        $this->disposition = $r->disposition ?? '';
        $this->pets = Pet::orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->petDeathRecordId = null;
        $this->pet_id = '';
        $this->death_date = '';
        $this->cause = '';
        $this->attending_vet = '';
        $this->notes = '';
        $this->disposition = '';
        $this->pets = Pet::orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['cause', 'attending_vet', 'notes', 'disposition'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->validate();

        $data = [
            'pet_id' => $this->pet_id,
            'death_date' => $this->death_date,
            'cause' => $this->cause,
            'attending_vet' => $this->attending_vet,
            'notes' => $this->notes,
            'disposition' => $this->disposition,
        ];

        if ($this->petDeathRecordId) {
            PetDeathRecord::findOrFail($this->petDeathRecordId)->update($data);
        } else {
            $data['registered_by'] = auth()->id();
            PetDeathRecord::create($data);
        }

        $this->dispatch('pet-death-record-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.pet-death-record-form');
    }
}
