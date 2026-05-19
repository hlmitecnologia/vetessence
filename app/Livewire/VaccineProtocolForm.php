<?php

namespace App\Livewire;

use App\Models\VaccineProtocol;
use Livewire\Attributes\On;
use Livewire\Component;

class VaccineProtocolForm extends Component
{
    public $vaccineProtocolId;
    public $species = '';
    public $vaccine_name = '';
    public $age_start_weeks = '';
    public $age_end_weeks = '';
    public $is_initial = false;
    public $dose_number = '';
    public $booster_interval_months = '';
    public $is_core = false;
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'species' => 'required|string|max:50',
        'vaccine_name' => 'required|string|max:200',
        'age_start_weeks' => 'nullable|integer|min:0',
        'age_end_weeks' => 'nullable|integer|min:0',
        'is_initial' => 'boolean',
        'dose_number' => 'nullable|integer|min:1',
        'booster_interval_months' => 'nullable|integer|min:1',
        'is_core' => 'boolean',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editVaccineProtocol')]
    public function load($id)
    {
        $this->vaccineProtocolId = $id;
        $protocol = VaccineProtocol::findOrFail($id);
        $this->species = $protocol->species;
        $this->vaccine_name = $protocol->vaccine_name;
        $this->age_start_weeks = (string) ($protocol->age_start_weeks ?? '');
        $this->age_end_weeks = (string) ($protocol->age_end_weeks ?? '');
        $this->is_initial = $protocol->is_initial;
        $this->dose_number = (string) ($protocol->dose_number ?? '');
        $this->booster_interval_months = (string) ($protocol->booster_interval_months ?? '');
        $this->is_core = $protocol->is_core;
        $this->notes = $protocol->notes ?? '';
        $this->is_active = $protocol->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->vaccineProtocolId = null;
        $this->species = '';
        $this->vaccine_name = '';
        $this->age_start_weeks = '';
        $this->age_end_weeks = '';
        $this->is_initial = false;
        $this->dose_number = '';
        $this->booster_interval_months = '';
        $this->is_core = false;
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['age_start_weeks', 'age_end_weeks', 'dose_number', 'booster_interval_months', 'notes'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->is_initial = (bool) $this->is_initial;
        $this->is_core = (bool) $this->is_core;
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'species' => $this->species,
            'vaccine_name' => $this->vaccine_name,
            'age_start_weeks' => $this->age_start_weeks,
            'age_end_weeks' => $this->age_end_weeks,
            'is_initial' => $this->is_initial,
            'dose_number' => $this->dose_number,
            'booster_interval_months' => $this->booster_interval_months,
            'is_core' => $this->is_core,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->vaccineProtocolId) {
            VaccineProtocol::findOrFail($this->vaccineProtocolId)->update($data);
        } else {
            VaccineProtocol::create($data);
        }

        $this->dispatch('vaccine-protocol-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.vaccine-protocol-form');
    }
}
