<?php

namespace App\Livewire;

use App\Models\WeightRecord;
use App\Models\Pet;
use Livewire\Attributes\On;
use Livewire\Component;

class WeightRecordForm extends Component
{
    public $weightRecordId;
    public $pet_id = '';
    public $weight = '';
    public $bcs = '';
    public $measurement_date = '';
    public $notes = '';

    public $pets = [];

    protected $rules = [
        'pet_id' => 'required|exists:pets,id',
        'weight' => 'required|numeric|min:0',
        'bcs' => 'nullable|numeric|min:1|max:9',
        'measurement_date' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function mount($id = null)
    {
        $this->pets = Pet::orderBy('name')->get();
        if ($id) $this->load($id);
    }

    #[On('editWeightRecord')]
    public function load($id)
    {
        $this->weightRecordId = $id;
        $record = WeightRecord::findOrFail($id);
        $this->pet_id = (string) $record->pet_id;
        $this->weight = (string) $record->weight;
        $this->bcs = (string) ($record->bcs ?? '');
        $this->measurement_date = $record->measurement_date->format('Y-m-d');
        $this->notes = $record->notes ?? '';
        $this->pets = Pet::orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->weightRecordId = null;
        $this->pet_id = '';
        $this->weight = '';
        $this->bcs = '';
        $this->measurement_date = '';
        $this->notes = '';
        $this->pets = Pet::orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['bcs', 'notes'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->validate();

        $data = [
            'pet_id' => $this->pet_id,
            'weight' => $this->weight,
            'bcs' => $this->bcs,
            'measurement_date' => $this->measurement_date,
            'notes' => $this->notes,
            'measured_by' => auth()->id(),
        ];

        if ($this->weightRecordId) {
            unset($data['measured_by']);
            WeightRecord::findOrFail($this->weightRecordId)->update($data);
        } else {
            WeightRecord::create($data);
        }

        $this->dispatch('weight-record-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.weight-record-form');
    }
}
