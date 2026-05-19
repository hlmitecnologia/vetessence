<?php

namespace App\Livewire;

use App\Models\ConsentTemplate;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

class ConsentTemplateForm extends Component
{
    public $consentTemplateId;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $content = '';
    public $category = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'content' => 'required|string',
        'category' => 'nullable|string|max:100',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editConsentTemplate')]
    public function load($id)
    {
        $this->consentTemplateId = $id;
        $template = ConsentTemplate::findOrFail($id);
        $this->name = $template->name;
        $this->slug = $template->slug ?? '';
        $this->description = $template->description ?? '';
        $this->content = $template->content;
        $this->category = $template->category ?? '';
        $this->is_active = $template->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->consentTemplateId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->content = '';
        $this->category = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->description = $this->description ?: null;
        $this->category = $this->category ?: null;
        $this->slug = $this->slug ?: null;
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?? Str::slug($this->name),
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'is_active' => $this->is_active,
        ];

        if ($this->consentTemplateId) {
            $template = ConsentTemplate::findOrFail($this->consentTemplateId);
            $data['slug'] = $this->slug ?? Str::slug($this->name);
            $template->update($data);
        } else {
            $data['slug'] = $this->slug ?? Str::slug($this->name);
            ConsentTemplate::create($data);
        }

        $this->dispatch('consent-template-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.consent-template-form');
    }
}
