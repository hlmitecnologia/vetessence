<?php

namespace App\Livewire;

use App\Models\Tutor;
use Livewire\Component;

class InvoiceForm extends Component
{
    public $tutor_id = '';
    public $due_date = '';
    public $items = [];
    public $tutors = [];
    public $tutorPets = [];

    protected $rules = [
        'tutor_id' => 'required|exists:tutors,id',
        'due_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.description' => 'required|string',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ];

    public function mount($tutorId = null)
    {
        $this->tutors = Tutor::orderBy('name')->get();
        $this->due_date = date('Y-m-d', strtotime('+7 days'));
        
        if ($tutorId) {
            $this->tutor_id = $tutorId;
            $this->loadTutorPets();
        }
        
        $this->addItem();
    }

    public function updatedTutorId($value)
    {
        $this->loadTutorPets();
    }

    public function loadTutorPets()
    {
        if ($this->tutor_id) {
            $tutor = Tutor::with('pets')->find($this->tutor_id);
            $this->tutorPets = $tutor ? $tutor->pets : [];
        } else {
            $this->tutorPets = [];
        }
    }

    public function addItem()
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum(function ($item) {
            return ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
        });
    }

    public function save()
    {
        $this->validate();

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => \App\Models\Invoice::generateNumber(),
            'tutor_id' => $this->tutor_id,
            'subtotal' => $this->subtotal,
            'discount' => 0,
            'total' => $this->subtotal,
            'due_date' => $this->due_date,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        session()->flash('success', 'Fatura criada com sucesso!');
        return redirect()->route('invoices.show', $invoice);
    }

    public function render()
    {
        return view('livewire.invoice-form');
    }
}
