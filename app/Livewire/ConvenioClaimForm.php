<?php

namespace App\Livewire;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Invoice;
use Livewire\Attributes\On;
use Livewire\Component;

class ConvenioClaimForm extends Component
{
    public $convenioClaimId;
    public $convenio_pet_id = '';
    public $invoice_id = '';
    public $amount_requested = '';
    public $notes = '';

    public $convenioPets = [];
    public $invoices = [];

    protected $rules = [
        'convenio_pet_id' => 'required|exists:convenio_pet,id',
        'invoice_id' => 'nullable|exists:invoices,id',
        'amount_requested' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    public function mount($id = null)
    {
        $this->convenioPets = ConvenioPet::with(['convenio', 'pet'])->get();
        $this->invoices = Invoice::orderBy('created_at', 'desc')->get();
        if ($id) $this->load($id);
    }

    #[On('editConvenioClaim')]
    public function load($id)
    {
        $this->convenioClaimId = $id;
        $claim = ConvenioClaim::findOrFail($id);
        $this->convenio_pet_id = (string) $claim->convenio_pet_id;
        $this->invoice_id = (string) ($claim->invoice_id ?? '');
        $this->amount_requested = (string) $claim->amount_requested;
        $this->notes = $claim->notes ?? '';
        $this->convenioPets = ConvenioPet::with(['convenio', 'pet'])->get();
        $this->invoices = Invoice::orderBy('created_at', 'desc')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->convenioClaimId = null;
        $this->convenio_pet_id = '';
        $this->invoice_id = '';
        $this->amount_requested = '';
        $this->notes = '';
        $this->convenioPets = ConvenioPet::with(['convenio', 'pet'])->get();
        $this->invoices = Invoice::orderBy('created_at', 'desc')->get();
        $this->resetValidation();
    }

    public function save()
    {
        $this->invoice_id = $this->invoice_id ?: null;
        $this->notes = $this->notes ?: null;
        $this->validate();

        $data = [
            'convenio_pet_id' => $this->convenio_pet_id,
            'invoice_id' => $this->invoice_id,
            'amount_requested' => $this->amount_requested,
            'notes' => $this->notes,
        ];

        if ($this->convenioClaimId) {
            ConvenioClaim::findOrFail($this->convenioClaimId)->update($data);
        } else {
            $data['claim_number'] = 'CLM-' . strtoupper(uniqid());
            $data['status'] = 'draft';
            ConvenioClaim::create($data);
        }

        $this->dispatch('convenio-claim-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.convenio-claim-form');
    }
}
