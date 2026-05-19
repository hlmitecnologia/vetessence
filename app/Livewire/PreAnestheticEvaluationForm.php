<?php

namespace App\Livewire;

use App\Models\PreAnestheticEvaluation;
use App\Models\Pet;
use Livewire\Attributes\On;
use Livewire\Component;

class PreAnestheticEvaluationForm extends Component
{
    public $preAnestheticEvaluationId;
    public $pet_id = '';
    public $asa_score = '1';
    public $fasted = false;
    public $hydrated = false;
    public $exam_checklist = [];
    public $observations = '';
    public $recommendations = '';
    public $status = 'pending';

    public $pets = [];

    public $examOptions = [
        'hemogram' => 'Hemograma',
        'biochemistry' => 'Bioquímica',
        'coagulation' => 'Coagulação',
        'ecg' => 'ECG',
        'chest_xray' => 'Raio-x Tórax',
        'echo' => 'Ecocardiograma',
    ];

    protected $rules = [
        'pet_id' => 'required|exists:pets,id',
        'asa_score' => 'required|integer|in:1,2,3,4,5,6',
        'fasted' => 'boolean',
        'hydrated' => 'boolean',
        'exam_checklist' => 'nullable|array',
        'observations' => 'nullable|string',
        'recommendations' => 'nullable|string',
        'status' => 'required|in:pending,approved,rejected',
    ];

    public function mount($id = null)
    {
        $this->pets = Pet::orderBy('name')->get();
        if ($id) $this->load($id);
    }

    #[On('editPreAnestheticEvaluation')]
    public function load($id)
    {
        $this->preAnestheticEvaluationId = $id;
        $eval = PreAnestheticEvaluation::findOrFail($id);
        $this->pet_id = (string) $eval->pet_id;
        $this->asa_score = (string) $eval->asa_score;
        $this->fasted = $eval->fasted;
        $this->hydrated = $eval->hydrated;
        $this->exam_checklist = $eval->exam_checklist ?? [];
        $this->observations = $eval->observations ?? '';
        $this->recommendations = $eval->recommendations ?? '';
        $this->status = $eval->status;
        $this->pets = Pet::orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->preAnestheticEvaluationId = null;
        $this->pet_id = '';
        $this->asa_score = '1';
        $this->fasted = false;
        $this->hydrated = false;
        $this->exam_checklist = [];
        $this->observations = '';
        $this->recommendations = '';
        $this->status = 'pending';
        $this->pets = Pet::orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['observations', 'recommendations'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->fasted = (bool) $this->fasted;
        $this->hydrated = (bool) $this->hydrated;
        $this->validate();

        $data = [
            'pet_id' => $this->pet_id,
            'asa_score' => $this->asa_score,
            'fasted' => $this->fasted,
            'hydrated' => $this->hydrated,
            'exam_checklist' => $this->exam_checklist,
            'observations' => $this->observations,
            'recommendations' => $this->recommendations,
            'status' => $this->status,
        ];

        if ($this->preAnestheticEvaluationId) {
            PreAnestheticEvaluation::findOrFail($this->preAnestheticEvaluationId)->update($data);
        } else {
            $data['vet_id'] = auth()->id();
            PreAnestheticEvaluation::create($data);
        }

        $this->dispatch('pre-anesthetic-evaluation-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.pre-anesthetic-evaluation-form');
    }
}
