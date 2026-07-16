<?php

namespace App\Livewire;

use App\Models\Convenio;
use App\Models\ConvenioCoverageRule;
use App\Models\Service;
use Livewire\Component;

class ConvenioCoverageRules extends Component
{
    public Convenio $convenio;
    public $rules = [];
    public $editIndex = null;

    protected function rules()
    {
        return [
            'rules.*.item_type' => 'required|string|max:50',
            'rules.*.service_id' => 'nullable|exists:services,id',
            'rules.*.coverage_percent' => 'required|numeric|min:0|max:100',
            'rules.*.max_value' => 'nullable|numeric|min:0',
            'rules.*.requires_pre_authorization' => 'boolean',
            'rules.*.annual_limit' => 'nullable|integer|min:0',
        ];
    }

    public function mount(Convenio $convenio)
    {
        $this->convenio = $convenio;
        $this->loadRules();
    }

    public function loadRules()
    {
        $this->rules = $this->convenio->coverageRules->map(function ($rule) {
            return [
                'id' => $rule->id,
                'item_type' => $rule->item_type,
                'service_id' => (string) ($rule->service_id ?? ''),
                'coverage_percent' => (string) $rule->coverage_percent,
                'max_value' => (string) ($rule->max_value ?? ''),
                'requires_pre_authorization' => $rule->requires_pre_authorization,
                'annual_limit' => (string) ($rule->annual_limit ?? ''),
            ];
        })->toArray();
    }

    public function addRule()
    {
        $this->rules[] = [
            'id' => null,
            'item_type' => 'service',
            'service_id' => '',
            'coverage_percent' => '100',
            'max_value' => '',
            'requires_pre_authorization' => false,
            'annual_limit' => '',
        ];
    }

    public function removeRule($index)
    {
        if (isset($this->rules[$index])) {
            if ($this->rules[$index]['id']) {
                ConvenioCoverageRule::destroy($this->rules[$index]['id']);
            }
            unset($this->rules[$index]);
            $this->rules = array_values($this->rules);
        }
    }

    public function saveRules()
    {
        $this->validate();

        foreach ($this->rules as $rule) {
            ConvenioCoverageRule::updateOrCreate(
                ['id' => $rule['id'] ?? null],
                [
                    'convenio_id' => $this->convenio->id,
                    'item_type' => $rule['item_type'],
                    'service_id' => $rule['service_id'] ?: null,
                    'coverage_percent' => $rule['coverage_percent'],
                    'max_value' => $rule['max_value'] ?: null,
                    'requires_pre_authorization' => $rule['requires_pre_authorization'] ?? false,
                    'annual_limit' => $rule['annual_limit'] ?: null,
                ]
            );
        }

        $this->convenio->refresh();
        $this->loadRules();
        $this->dispatch('rules-saved');
    }

    public function render()
    {
        $itemTypes = [
            'service' => 'Serviço',
            'product' => 'Produto',
            'procedure' => 'Procedimento',
        ];
        $services = Service::orderBy('name')->get();

        return view('livewire.convenio-coverage-rules', compact('itemTypes', 'services'));
    }
}
