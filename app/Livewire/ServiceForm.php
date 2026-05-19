<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceForm extends Component
{
    public $serviceId;
    public $name = '';
    public $category_id = '';
    public $description = '';
    public $price = '';
    public $duration = '';
    public $is_active = true;

    public $categories = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'duration' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->categories = Category::where('type', 'service')->orderBy('name')->get();
        if ($id) $this->load($id);
    }

    #[On('editService')]
    public function load($id)
    {
        $this->serviceId = $id;
        $service = Service::findOrFail($id);
        $this->name = $service->name;
        $this->category_id = (string) ($service->category_id ?? '');
        $this->description = $service->description ?? '';
        $this->price = (string) $service->price;
        $this->duration = (string) ($service->duration ?? '');
        $this->is_active = $service->is_active;
        $this->categories = Category::where('type', 'service')->orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->serviceId = null;
        $this->name = '';
        $this->category_id = '';
        $this->description = '';
        $this->price = '';
        $this->duration = '';
        $this->is_active = true;
        $this->categories = Category::where('type', 'service')->orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['description', 'duration'] as $f) {
            $this->$f = $this->$f !== '' && $this->$f !== null ? $this->$f : null;
        }
        $this->category_id = $this->category_id ?: null;
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'duration' => $this->duration,
            'is_active' => $this->is_active,
        ];

        if ($this->serviceId) {
            Service::findOrFail($this->serviceId)->update($data);
        } else {
            Service::create($data);
        }

        $this->dispatch('service-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.service-form');
    }
}
