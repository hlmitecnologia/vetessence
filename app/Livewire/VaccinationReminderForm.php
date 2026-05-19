<?php

namespace App\Livewire;

use App\Models\VaccinationReminder;
use App\Models\Pet;
use App\Models\Vaccination;
use Livewire\Attributes\On;
use Livewire\Component;

class VaccinationReminderForm extends Component
{
    public $vaccinationReminderId;
    public $vaccination_id = '';
    public $pet_id = '';
    public $scheduled_date = '';
    public $channel = '';
    public $status = 'pending';
    public $notes = '';

    public $pets = [];
    public $vaccinations = [];

    protected $rules = [
        'vaccination_id' => 'required|exists:vaccinations,id',
        'pet_id' => 'required|exists:pets,id',
        'scheduled_date' => 'required|date',
        'channel' => 'nullable|string|max:20',
        'status' => 'required|string|max:20',
        'notes' => 'nullable|string|max:500',
    ];

    public function mount($id = null)
    {
        $this->pets = Pet::where('is_active', true)->orderBy('name')->get();
        $this->vaccinations = Vaccination::orderBy('vaccine')->get();
        if ($id) $this->load($id);
    }

    #[On('editVaccinationReminder')]
    public function load($id)
    {
        $this->vaccinationReminderId = $id;
        $reminder = VaccinationReminder::findOrFail($id);
        $this->vaccination_id = (string) $reminder->vaccination_id;
        $this->pet_id = (string) $reminder->pet_id;
        $this->scheduled_date = $reminder->scheduled_date->format('Y-m-d');
        $this->channel = $reminder->channel ?? '';
        $this->status = $reminder->status;
        $this->notes = $reminder->notes ?? '';
        $this->vaccinations = Vaccination::orderBy('vaccine')->get();
        $this->pets = Pet::where('is_active', true)->orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->vaccinationReminderId = null;
        $this->vaccination_id = '';
        $this->pet_id = '';
        $this->scheduled_date = '';
        $this->channel = '';
        $this->status = 'pending';
        $this->notes = '';
        $this->vaccinations = Vaccination::orderBy('vaccine')->get();
        $this->pets = Pet::where('is_active', true)->orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['channel', 'notes'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->validate();

        $data = [
            'vaccination_id' => $this->vaccination_id,
            'pet_id' => $this->pet_id,
            'scheduled_date' => $this->scheduled_date,
            'channel' => $this->channel,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->vaccinationReminderId) {
            VaccinationReminder::findOrFail($this->vaccinationReminderId)->update($data);
        } else {
            VaccinationReminder::create($data);
        }

        $this->dispatch('vaccination-reminder-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.vaccination-reminder-form');
    }
}
