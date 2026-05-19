<?php

namespace App\Livewire;

use App\Models\CommunicationTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class CommunicationTemplateForm extends Component
{
    public $communicationTemplateId;
    public $name = '';
    public $type = '';
    public $channel = '';
    public $subject = '';
    public $content = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:100',
        'channel' => 'required|string|max:50',
        'subject' => 'nullable|string|max:255',
        'content' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) $this->load($id);
    }

    #[On('editCommunicationTemplate')]
    public function load($id)
    {
        $this->communicationTemplateId = $id;
        $template = CommunicationTemplate::findOrFail($id);
        $this->name = $template->name;
        $this->type = $template->type;
        $this->channel = $template->channel;
        $this->subject = $template->subject ?? '';
        $this->content = $template->content;
        $this->is_active = $template->is_active;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->communicationTemplateId = null;
        $this->name = '';
        $this->type = '';
        $this->channel = '';
        $this->subject = '';
        $this->content = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->subject = $this->subject ?: null;
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'channel' => $this->channel,
            'subject' => $this->subject,
            'content' => $this->content,
            'is_active' => $this->is_active,
        ];

        if ($this->communicationTemplateId) {
            CommunicationTemplate::findOrFail($this->communicationTemplateId)->update($data);
        } else {
            CommunicationTemplate::create($data);
        }

        $this->dispatch('communication-template-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.communication-template-form');
    }
}
