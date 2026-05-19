<?php

namespace App\Livewire;

use App\Models\GroomingTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class GroomingTemplateForm extends Component
{
    public $groomingTemplateId;
    public $name = '';
    public $species = '';
    public $breed = '';
    public $size = '';
    public $services = '';
    public $price = '';
    public $estimated_minutes = '';
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'species' => 'nullable|string|max:50',
        'breed' => 'nullable|string|max:100',
        'size' => 'nullable|string|max:20',
        'services' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'estimated_minutes' => 'required|integer|min:1',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editGroomingTemplate')]
    public function load($id)
    {
        $this->groomingTemplateId = $id;
        $template = GroomingTemplate::findOrFail($id);
        $this->name = $template->name;
        $this->species = $template->species ?? '';
        $this->breed = $template->breed ?? '';
        $this->size = $template->size ?? '';
        $this->services = $template->services ? json_encode($template->services) : '';
        $this->price = (string) $template->price;
        $this->estimated_minutes = (string) $template->estimated_minutes;
        $this->notes = $template->notes ?? '';
        $this->is_active = $template->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->groomingTemplateId = null;
        $this->name = '';
        $this->species = '';
        $this->breed = '';
        $this->size = '';
        $this->services = '';
        $this->price = '';
        $this->estimated_minutes = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['species', 'breed', 'size', 'notes'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'size' => $this->size,
            'services' => $this->services ? json_decode($this->services, true) : null,
            'price' => $this->price,
            'estimated_minutes' => $this->estimated_minutes,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->groomingTemplateId) {
            GroomingTemplate::findOrFail($this->groomingTemplateId)->update($data);
        } else {
            GroomingTemplate::create($data);
        }

        $this->dispatch('grooming-template-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.grooming-template-form');
    }
}
