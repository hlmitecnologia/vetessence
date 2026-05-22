<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\Supplier;
use App\Services\Cep\CepService;
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
    public $number = '';
    public $neighborhood = '';
    public $complement = '';
    public $state_id = '';
    public $city_id = '';
    public $zipcode = '';
    public $contact = '';
    public $notes = '';
    public $branch_id = '';
    public $branches = [];
    public $states = [];
    public $cities = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'cnpj' => 'nullable|string|max:18',
        'ie' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'address' => 'nullable|string',
        'number' => 'nullable|string|max:20',
        'neighborhood' => 'nullable|string|max:100',
        'complement' => 'nullable|string|max:100',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
        'zipcode' => 'nullable|string|max:20',
        'contact' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
        'branch_id' => 'nullable|exists:branches,id',
    ];

    public function mount($id = null)
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->states = State::orderBy('name')->pluck('name', 'id')->toArray();
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
        $this->number = $sup->number ?? '';
        $this->neighborhood = $sup->neighborhood ?? '';
        $this->complement = $sup->complement ?? '';
        $this->state_id = (string) ($sup->state_id ?? '');
        $this->city_id = (string) ($sup->city_id ?? '');
        $this->zipcode = $sup->zipcode ?? '';
        $this->contact = $sup->contact ?? '';
        $this->notes = $sup->notes ?? '';
        $this->branch_id = (string) ($sup->branch_id ?? '');
        $this->loadCities();
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
        $this->number = '';
        $this->neighborhood = '';
        $this->complement = '';
        $this->state_id = '';
        $this->city_id = '';
        $this->zipcode = '';
        $this->contact = '';
        $this->notes = '';
        $this->branch_id = '';
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
        foreach (['cnpj', 'ie', 'phone', 'email', 'address', 'number', 'neighborhood', 'complement', 'zipcode', 'contact', 'notes'] as $f) {
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
            'number' => $this->number,
            'neighborhood' => $this->neighborhood,
            'complement' => $this->complement,
            'state_id' => $this->state_id ?: null,
            'city_id' => $this->city_id ?: null,
            'zipcode' => $this->zipcode,
            'contact' => $this->contact,
            'notes' => $this->notes,
            'branch_id' => $this->branch_id ?: null,
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
