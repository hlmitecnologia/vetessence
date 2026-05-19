<?php

namespace App\Livewire;

use App\Models\ClinicalReportTemplate;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

class ClinicalReportTemplateForm extends Component
{
    public $clinicalReportTemplateId;
    public $name = '';
    public $slug = '';
    public $species = '';
    public $specialty = '';
    public $category = '';
    public $description = '';
    public $content = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'species' => 'nullable|string|max:50',
        'specialty' => 'nullable|string|max:100',
        'category' => 'nullable|string|max:100',
        'description' => 'nullable|string',
        'content' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editClinicalReportTemplate')]
    public function load($id)
    {
        $this->clinicalReportTemplateId = $id;
        $template = ClinicalReportTemplate::findOrFail($id);
        $this->name = $template->name;
        $this->slug = $template->slug ?? '';
        $this->species = $template->species ?? '';
        $this->specialty = $template->specialty ?? '';
        $this->category = $template->category ?? '';
        $this->description = $template->description ?? '';
        $this->content = $template->content;
        $this->is_active = $template->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->clinicalReportTemplateId = null;
        $this->name = '';
        $this->slug = '';
        $this->species = '';
        $this->specialty = '';
        $this->category = '';
        $this->description = '';
        $this->content = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['slug', 'species', 'specialty', 'category', 'description'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?? Str::slug($this->name),
            'species' => $this->species,
            'specialty' => $this->specialty,
            'category' => $this->category,
            'description' => $this->description,
            'content' => $this->content,
            'is_active' => $this->is_active,
        ];

        if ($this->clinicalReportTemplateId) {
            ClinicalReportTemplate::findOrFail($this->clinicalReportTemplateId)->update($data);
        } else {
            ClinicalReportTemplate::create($data);
        }

        $this->dispatch('clinical-report-template-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.clinical-report-template-form');
    }
}
