<?php

namespace App\Livewire;

use App\Models\Tutor;
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
    public $city = '';
    public $state = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'cpf' => 'required|string|unique:tutors,cpf',
        'email' => 'required|email|unique:tutors,email',
        'phone' => 'required',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:2',
    ];

    public function mount($id = null)
    {
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
        $this->city = $tutor->city ?? '';
        $this->state = $tutor->state ?? '';
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
        $this->city = '';
        $this->state = '';
        $this->resetValidation();
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->tutorId) {
            $rules['cpf'] = 'required|string|unique:tutors,cpf,' . $this->tutorId;
            $rules['email'] = 'required|email|unique:tutors,email,' . $this->tutorId;
        }
        $this->validate($rules);

        foreach (['address', 'city', 'state'] as $f) {
            $this->$f = $this->$f ?: null;
        }

        $data = [
            'name' => $this->name,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
        ];

        if ($this->tutorId) {
            Tutor::findOrFail($this->tutorId)->update($data);
        } else {
            Tutor::create($data);
        }

        $this->dispatch('tutor-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.tutor-form');
    }
}
