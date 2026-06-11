<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\State;
use App\Models\Tutor;
use App\Services\Cep\CepService;
use Livewire\Attributes\On;
use Livewire\Component;

class TutorForm extends Component
{
    public $tutorId;
    public $name = '';
    public $cpf = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $number = '';
    public $neighborhood = '';
    public $complement = '';
    public $zipcode = '';
    public $state_id = '';
    public $city_id = '';
    public $states = [];
    public $cities = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'cpf' => 'required|string|unique:tutors,cpf',
        'email' => 'required|email|unique:tutors,email',
        'phone' => 'required',
        'address' => 'nullable|string',
        'number' => 'nullable|string|max:20',
        'neighborhood' => 'nullable|string|max:100',
        'complement' => 'nullable|string|max:100',
        'zipcode' => 'nullable|string|max:20',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
    ];

    public function mount($id = null)
    {
        $this->states = State::orderBy('name')->pluck('name', 'id')->toArray();
        if ($id) $this->load($id);
    }

    #[On('editTutor')]
    public function load($id)
    {
        $this->tutorId = $id;
        $tutor = Tutor::findOrFail($id);
        $this->name = $tutor->name;
        $this->cpf = $tutor->cpf;
        $this->email = $tutor->email;
        $this->phone = $tutor->phone;
        $this->address = $tutor->address ?? '';
        $this->number = $tutor->number ?? '';
        $this->neighborhood = $tutor->neighborhood ?? '';
        $this->complement = $tutor->complement ?? '';
        $this->zipcode = $tutor->zipcode ?? '';
        $this->state_id = (string) ($tutor->state_id ?? '');
        $this->city_id = (string) ($tutor->city_id ?? '');
        $this->loadCities();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->tutorId = null;
        $this->name = '';
        $this->cpf = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->number = '';
        $this->neighborhood = '';
        $this->complement = '';
        $this->zipcode = '';
        $this->state_id = '';
        $this->city_id = '';
        $this->cities = [];
        $this->resetValidation();
    }

    public function updatedStateId($value)
    {
        $this->city_id = '';
        $this->cities = [];
        if ($value) {
            $this->loadCities();
        }
    }

    public function updatedZipcode($value)
    {
        $cep = preg_replace('/\D/', '', $value ?? '');
        if (strlen($cep) !== 8) {
            return;
        }

        $result = app(CepService::class)->lookup($cep);
        if (!$result) {
            return;
        }

        $this->address = $result->street;
        $this->neighborhood = $result->neighborhood;

        $state = State::where('uf', $result->state)->first();
        if ($state) {
            $this->state_id = (string) $state->id;
            $this->loadCities();

            $city = City::where('state_id', $state->id)
                ->where('name', $result->city)
                ->first();
            if ($city) {
                $this->city_id = (string) $city->id;
            }
        }
    }

    protected function loadCities()
    {
        if ($this->state_id) {
            $this->cities = City::where('state_id', $this->state_id)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function save()
    {
        $this->state_id = $this->state_id ?: null;
        $this->city_id = $this->city_id ?: null;

        if (!$this->tutorId && $this->cpf) {
            $existing = Tutor::where('cpf', $this->cpf)->first();
            if ($existing && $existing->name === $this->name) {
                $this->tutorId = $existing->id;
            }
        }

        $rules = $this->rules;
        if ($this->tutorId) {
            $rules['cpf'] = 'required|string|unique:tutors,cpf,' . $this->tutorId;
            $rules['email'] = 'required|email|unique:tutors,email,' . $this->tutorId;
        }

        $this->validate($rules);

        foreach (['address', 'number', 'neighborhood', 'complement', 'zipcode'] as $f) {
            $this->$f = $this->$f ?: null;
        }

        $data = [
            'name' => $this->name,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'number' => $this->number,
            'neighborhood' => $this->neighborhood,
            'complement' => $this->complement,
            'zipcode' => $this->zipcode,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'notify_sms' => true,
            'notify_whatsapp' => true,
            'notify_email' => true,
        ];

        try {
            if ($this->tutorId) {
                Tutor::findOrFail($this->tutorId)->update($data);
            } else {
                Tutor::create($data);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar tutor: ' . $e->getMessage());
            return;
        }

        $this->dispatch('tutor-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.tutor-form');
    }
}
