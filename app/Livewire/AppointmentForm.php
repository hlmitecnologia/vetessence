<?php

namespace App\Livewire;

use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use App\Models\Role;
use Livewire\Component;

class AppointmentForm extends Component
{
    public $pet_id = '';
    public $vet_id = '';
    public $date = '';
    public $time = '';
    public $type = 'consulta';
    public $reason = '';
    public $selectedServices = [];
    
    public $pets = [];
    public $veterinarians = [];
    public $services = [];
    
    public $total = 0;

    protected $rules = [
        'pet_id' => 'required|exists:pets,id',
        'vet_id' => 'required|exists:users,id',
        'date' => 'required|date|after_or_equal:today',
        'time' => 'required',
        'type' => 'required|in:consulta,retorno,emergencia,cirurgia,vacina,exame',
    ];

    public function mount()
    {
        $this->pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $this->veterinarians = $this->getVeterinarians();
        $this->services = Service::where('is_active', true)->orderBy('name')->get();
        $this->date = date('Y-m-d');
        $this->time = date('H:00');
    }

    protected function getVeterinarians()
    {
        return User::where('is_active', true)->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))->orderBy('name')->get();
    }

    public function updatedSelectedServices($value)
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->selectedServices)->sum(function ($serviceId) {
            $service = Service::find($serviceId);
            return $service ? $service->price : 0;
        });
    }

    public function save()
    {
        $this->validate();

        $appointment = \App\Models\Appointment::create([
            'pet_id' => $this->pet_id,
            'vet_id' => $this->vet_id,
            'date' => $this->date,
            'time' => $this->time,
            'type' => $this->type,
            'reason' => $this->reason,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        foreach ($this->selectedServices as $serviceId) {
            $service = Service::find($serviceId);
            \App\Models\AppointmentService::create([
                'appointment_id' => $appointment->id,
                'service_id' => $serviceId,
                'price' => $service->price,
                'quantity' => 1,
            ]);
        }

        session()->flash('success', 'Consulta agendada com sucesso!');
        return redirect()->route('appointments.index');
    }

    public function render()
    {
        return view('livewire.appointment-form');
    }
}
