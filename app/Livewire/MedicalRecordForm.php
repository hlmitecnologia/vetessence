<?php

namespace App\Livewire;

use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Appointment;
use App\Models\ZoonoticDisease;
use App\Services\Llm\LlmService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MedicalRecordForm extends Component
{
    public $pet_id = '';
    public $vet_id = '';
    public $appointment_id = '';
    public $date = '';
    public $time = '';
    public $type = 'consulta';
    public $chief_complaint = '';
    public $anamnesis = '';
    public $physical_exam = '';
    public $vital_signs = [
        'temperature' => '',
        'heart_rate' => '',
        'respiratory_rate' => '',
        'weight' => '',
        'mucosa' => '',
        'hydration' => '',
        'lymph_nodes' => '',
    ];
    public $diagnosis = '';
    public $treatment = '';
    public $prognosis = '';
    public $notes = '';
    public $prescriptions = [];
    public $selectedDiseases = [];

    public $suggestingDiagnosis = false;
    public $suggestionError = '';

    public $pets = [];
    public $veterinarians = [];
    public $appointments = [];
    public $zoonoticDiseases = [];
    public $editMode = false;
    public $recordId = null;

    protected $rules = [
        'pet_id' => 'required|exists:pets,id',
        'vet_id' => 'required|exists:users,id',
        'date' => 'required|date',
        'time' => 'required',
        'type' => 'required|in:consulta,cirurgia,emergencia,vacina,retorno,exame',
        'chief_complaint' => 'nullable|string',
        'anamnesis' => 'nullable|string',
        'physical_exam' => 'nullable|string',
        'vital_signs.temperature' => 'nullable|string|max:20',
        'vital_signs.heart_rate' => 'nullable|string|max:20',
        'vital_signs.respiratory_rate' => 'nullable|string|max:20',
        'vital_signs.weight' => 'nullable|string|max:20',
        'vital_signs.mucosa' => 'nullable|string|max:50',
        'vital_signs.hydration' => 'nullable|string|max:50',
        'vital_signs.lymph_nodes' => 'nullable|string|max:50',
        'diagnosis' => 'nullable|string',
        'treatment' => 'nullable|string',
        'prognosis' => 'nullable|in:bom,reservado,grave,obito',
        'notes' => 'nullable|string',
        'prescriptions' => 'nullable|array',
        'prescriptions.*.medication' => 'required_with:prescriptions|string',
        'prescriptions.*.dosage' => 'nullable|string',
        'prescriptions.*.unit' => 'nullable|string',
        'prescriptions.*.frequency' => 'nullable|string',
        'prescriptions.*.duration' => 'nullable|string',
        'prescriptions.*.route' => 'nullable|string',
        'prescriptions.*.instructions' => 'nullable|string',
        'selectedDiseases' => 'nullable|array',
        'selectedDiseases.*.disease_id' => 'required_with:selectedDiseases|exists:zoonotic_diseases,id',
        'selectedDiseases.*.is_suspected' => 'boolean',
    ];

    public function mount($recordId = null, $petId = null, $appointmentId = null)
    {
        $this->pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $this->veterinarians = $this->getVeterinarians();
        $this->zoonoticDiseases = ZoonoticDisease::active()->orderBy('name')->get();
        $this->date = date('Y-m-d');
        $this->time = date('H:i');

        if ($petId) {
            $this->pet_id = $petId;
        }

        if ($appointmentId) {
            $this->appointment_id = $appointmentId;
            $appointment = Appointment::with('pet')->find($appointmentId);
            if ($appointment) {
                $this->pet_id = $appointment->pet_id;
                $this->vet_id = $appointment->vet_id;
            }
        }

        if ($recordId) {
            $this->editMode = true;
            $this->recordId = $recordId;

            $record = MedicalRecord::with('appointment')->find($recordId);
            if ($record && $record->appointment && $record->appointment->hasPaidInvoice()) {
                session()->flash('error', 'Este prontuário não pode ser editado porque o atendimento já possui uma fatura paga.');
                return redirect()->route('medical-records.show', $recordId);
            }

            $this->loadRecord();
        }
    }

    protected function getVeterinarians()
    {
        $vetRole = Role::where('slug', 'veterinario')->first();
        if (!$vetRole) {
            return collect();
        }
        return User::where('role_id', $vetRole->id)->where('is_active', true)->orderBy('name')->get();
    }

    public function loadRecord()
    {
        $record = MedicalRecord::with('prescriptions')->findOrFail($this->recordId);
        $this->pet_id = $record->pet_id;
        $this->vet_id = $record->vet_id;
        $this->appointment_id = $record->appointment_id;
        $this->date = $record->date->format('Y-m-d');
        $this->time = $record->time ? $record->time->format('H:i') : '';
        $this->type = $record->type;
        $this->chief_complaint = $record->chief_complaint;
        $this->anamnesis = $record->anamnesis;
        $this->physical_exam = $record->physical_exam;
        $this->diagnosis = $record->diagnosis;
        $this->treatment = $record->treatment;
        $this->prognosis = $record->prognosis;
        $this->notes = $record->notes;

        if ($record->vital_signs) {
            $this->vital_signs = array_merge($this->vital_signs, $record->vital_signs);
        }

        $this->selectedDiseases = $record->zoonoticDiseases->map(function ($disease) {
            return [
                'disease_id' => (string) $disease->id,
                'is_suspected' => $disease->pivot->is_suspected,
            ];
        })->toArray();

        $this->prescriptions = $record->prescriptions->map(function ($rx) {
            return [
                'medication' => $rx->medication,
                'dosage' => $rx->dosage,
                'unit' => $rx->unit,
                'frequency' => $rx->frequency,
                'duration' => $rx->duration,
                'route' => $rx->route,
                'instructions' => $rx->instructions,
            ];
        })->toArray();
    }

    public function addDisease()
    {
        $this->selectedDiseases[] = [
            'disease_id' => '',
            'is_suspected' => false,
        ];
    }

    public function removeDisease($index)
    {
        unset($this->selectedDiseases[$index]);
        $this->selectedDiseases = array_values($this->selectedDiseases);
    }

    public function suggestDiagnosis()
    {
        if ($this->suggestingDiagnosis) {
            return;
        }

        $this->suggestingDiagnosis = true;
        $this->suggestionError = '';

        try {
            if ($this->recordId) {
                $record = MedicalRecord::findOrFail($this->recordId);
            } else {
                $record = new MedicalRecord();
                $record->pet_id = $this->pet_id;
                $record->chief_complaint = $this->chief_complaint;
                $record->anamnesis = $this->anamnesis;
                $record->physical_exam = $this->physical_exam;
                $record->vital_signs = array_filter($this->vital_signs);
            }

            $service = app(LlmService::class);
            $result = $service->suggestDiagnosis($record);

            if ($result->success && $result->content) {
                $this->diagnosis = $result->content;
            } else {
                $this->suggestionError = $result->errorMessage ?? 'Erro ao obter sugestão.';
            }
        } catch (\Exception $e) {
            $this->suggestionError = 'Erro ao comunicar com o provedor de IA.';
        } finally {
            $this->suggestingDiagnosis = false;
        }
    }

    public function addPrescription()
    {
        $this->prescriptions[] = [
            'medication' => '',
            'dosage' => '',
            'unit' => '',
            'frequency' => '',
            'duration' => '',
            'route' => 'oral',
            'instructions' => '',
        ];
    }

    public function removePrescription($index)
    {
        unset($this->prescriptions[$index]);
        $this->prescriptions = array_values($this->prescriptions);
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $data = [
                'pet_id' => $this->pet_id,
                'user_id' => $this->vet_id,
                'appointment_id' => $this->appointment_id ?: null,
                'date' => $this->date,
                'time' => $this->time,
                'type' => $this->type,
                'chief_complaint' => $this->chief_complaint ?: null,
                'anamnesis' => $this->anamnesis ?: null,
                'physical_exam' => $this->physical_exam ?: null,
                'vital_signs' => array_filter($this->vital_signs),
                'diagnosis' => $this->diagnosis ?: null,
                'treatment' => $this->treatment ?: null,
                'prognosis' => $this->prognosis ?: null,
                'notes' => $this->notes ?: null,
            ];

            if ($this->editMode) {
                $record = MedicalRecord::findOrFail($this->recordId);
                $record->update($data);
                $record->prescriptions()->delete();
            } else {
                $record = MedicalRecord::create($data);
            }

            if (!empty($this->selectedDiseases)) {
                $diseaseData = [];
                foreach ($this->selectedDiseases as $sd) {
                    if (!empty($sd['disease_id'])) {
                        $diseaseData[$sd['disease_id']] = [
                            'is_suspected' => $sd['is_suspected'] ?? false,
                        ];
                    }
                }
                $record->zoonoticDiseases()->sync($diseaseData);
            }

            foreach ($this->prescriptions as $prescription) {
                if (!empty($prescription['medication'])) {
                    Prescription::create([
                        'medical_record_id' => $record->id,
                        'medication' => $prescription['medication'],
                        'dosage' => $prescription['dosage'] ?? null,
                        'unit' => $prescription['unit'] ?? null,
                        'frequency' => $prescription['frequency'] ?? null,
                        'duration' => $prescription['duration'] ?? null,
                        'route' => $prescription['route'] ?? 'oral',
                        'instructions' => $prescription['instructions'] ?? null,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', $this->editMode
                ? 'Prontuário atualizado com sucesso!'
                : 'Prontuário criado com sucesso!');

            return redirect()->route('medical-records.show', $record);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erro ao salvar prontuário.');
        }
    }

    public function render()
    {
        return view('livewire.medical-record-form');
    }
}
