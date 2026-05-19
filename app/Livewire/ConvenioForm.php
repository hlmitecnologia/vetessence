<?php

namespace App\Livewire;

use App\Models\Convenio;
use Livewire\Attributes\On;
use Livewire\Component;

class ConvenioForm extends Component
{
    public $convenioId;
    public $name = '';
    public $cnpj = '';
    public $plan_name = '';
    public $coverage = '';
    public $discount_percent = '';
    public $max_consults_month = '';
    public $start_date = '';
    public $end_date = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'cnpj' => 'nullable|string|max:18',
        'plan_name' => 'nullable|string|max:100',
        'coverage' => 'nullable|string',
        'discount_percent' => 'nullable|numeric|min:0|max:100',
        'max_consults_month' => 'nullable|integer|min:1',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editConvenio')]
    public function load($id)
    {
        $this->convenioId = $id;
        $conv = Convenio::findOrFail($id);
        $this->name = $conv->name;
        $this->cnpj = $conv->cnpj ?? '';
        $this->plan_name = $conv->plan_name ?? '';
        $this->coverage = $conv->coverage ?? '';
        $this->discount_percent = (string) ($conv->discount_percent ?? '');
        $this->max_consults_month = (string) ($conv->max_consults_month ?? '');
        $this->start_date = $conv->start_date ? $conv->start_date->format('Y-m-d') : '';
        $this->end_date = $conv->end_date ? $conv->end_date->format('Y-m-d') : '';
        $this->is_active = $conv->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->convenioId = null;
        $this->name = '';
        $this->cnpj = '';
        $this->plan_name = '';
        $this->coverage = '';
        $this->discount_percent = '';
        $this->max_consults_month = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['cnpj', 'plan_name', 'coverage', 'discount_percent', 'max_consults_month', 'start_date', 'end_date'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'plan_name' => $this->plan_name,
            'coverage' => $this->coverage,
            'discount_percent' => $this->discount_percent,
            'max_consults_month' => $this->max_consults_month,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ];

        if ($this->convenioId) {
            Convenio::findOrFail($this->convenioId)->update($data);
        } else {
            Convenio::create($data);
        }

        $this->dispatch('convenio-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.convenio-form');
    }
}
