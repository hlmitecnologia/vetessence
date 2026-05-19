<?php

namespace App\Livewire;

use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

class SupplierForm extends Component
{
    public $supplierId;
    public $name = '';
    public $cnpj = '';
    public $ie = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $contact = '';
    public $notes = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'cnpj' => 'nullable|string|max:18',
        'ie' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:2',
        'contact' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editSupplier')]
    public function load($id)
    {
        $this->supplierId = $id;
        $sup = Supplier::findOrFail($id);
        $this->name = $sup->name;
        $this->cnpj = $sup->cnpj ?? '';
        $this->ie = $sup->ie ?? '';
        $this->phone = $sup->phone ?? '';
        $this->email = $sup->email ?? '';
        $this->address = $sup->address ?? '';
        $this->city = $sup->city ?? '';
        $this->state = $sup->state ?? '';
        $this->contact = $sup->contact ?? '';
        $this->notes = $sup->notes ?? '';
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->supplierId = null;
        $this->name = '';
        $this->cnpj = '';
        $this->ie = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->city = '';
        $this->state = '';
        $this->contact = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['cnpj', 'ie', 'phone', 'email', 'address', 'city', 'state', 'contact', 'notes'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->validate();

        $data = [
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'ie' => $this->ie,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'contact' => $this->contact,
            'notes' => $this->notes,
        ];

        if ($this->supplierId) {
            Supplier::findOrFail($this->supplierId)->update($data);
        } else {
            Supplier::create($data);
        }

        $this->dispatch('supplier-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.supplier-form');
    }
}
