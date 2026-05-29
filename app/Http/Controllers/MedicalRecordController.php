<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Role;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceTypeMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $records = $query->orderBy('date', 'desc')->paginate(20);

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('medical-records.index', compact('records', 'pets'));
    }

    public function create(Request $request)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        
        $selectedPet = $request->pet_id ? Pet::find($request->pet_id) : null;

        return view('medical-records.create', compact('pets', 'veterinarians', 'selectedPet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:consulta,cirurgia,emergencia,vacina,retorno,exame,consultation',
            'chief_complaint' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prognosis' => 'nullable|in:bom,reservado,grave,obito',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $record = MedicalRecord::create($validated);
            $record->sign();

            if ($request->prescriptions) {
                foreach ($request->prescriptions as $prescription) {
                    if (!empty($prescription['medication'])) {
                        Prescription::create([
                            'medical_record_id' => $record->id,
                            'medication' => $prescription['medication'],
                            'dosage' => $prescription['dosage'] ?? null,
                            'frequency' => $prescription['frequency'] ?? null,
                            'duration' => $prescription['duration'] ?? null,
                            'route' => $prescription['route'] ?? 'oral',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('medical-records.show', $record)->with('success', 'Prontuário criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar prontuário.')->withInput();
        }
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['pet.tutors', 'vet', 'prescriptions']);
        return view('medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        $medicalRecord->load('prescriptions');

        return view('medical-records.edit', compact('medicalRecord', 'pets', 'veterinarians'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'chief_complaint' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prognosis' => 'nullable|in:bom,reservado,grave,obito',
            'notes' => 'nullable|string',
        ]);

        $medicalRecord->update($validated);

        return redirect()->route('medical-records.show', $medicalRecord)->with('success', 'Prontuário atualizado!');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return redirect()->route('medical-records.index')->with('success', 'Registro excluído!');
    }

    public function generateInvoice(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['pet.tutors', 'vet']);

        $tutor = $medicalRecord->pet->tutors()
            ->wherePivot('is_primary', true)
            ->first() ?? $medicalRecord->pet->tutors()->first();

        if (!$tutor) {
            return back()->with('error', 'Pet não possui tutor cadastrado.');
        }

        $map = ServiceTypeMap::where('type', $medicalRecord->type)
            ->where(function ($q) use ($medicalRecord) {
                $q->whereNull('branch_id')
                  ->orWhere('branch_id', $medicalRecord->branch_id);
            })
            ->orderBy('branch_id', 'desc')
            ->first();

        $service = $map?->service;
        $unitPrice = $service?->price ?? 0;

        $existingInvoice = Invoice::where('tutor_id', $tutor->id)
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvoice) {
            $invoice = $existingInvoice;
            $invoice->update([
                'total' => $invoice->total + $unitPrice,
                'subtotal' => $invoice->subtotal + $unitPrice,
            ]);
            $notes = $invoice->notes
                ? $invoice->notes . " | Prontuário #{$medicalRecord->id}"
                : "Gerado a partir do prontuário #{$medicalRecord->id}";
            $invoice->update(['notes' => $notes]);
        } else {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'pet_id' => $medicalRecord->pet_id,
                'tutor_id' => $tutor->id,
                'user_id' => $medicalRecord->user_id,
                'branch_id' => $medicalRecord->branch_id,
                'status' => 'pending',
                'total' => $unitPrice,
                'subtotal' => $unitPrice,
                'due_date' => $medicalRecord->date,
                'notes' => "Gerado a partir do prontuário #{$medicalRecord->id}",
            ]);
        }

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $service?->name ?? $this->getTypeLabel($medicalRecord->type),
            'quantity' => 1,
            'unit_price' => $unitPrice,
            'total' => $unitPrice,
        ]);

        $msg = $unitPrice > 0
            ? ($existingInvoice
                ? "Atendimento adicionado à fatura {$invoice->invoice_number}!"
                : "Fatura criada com sucesso!")
            : "Fatura criada com valor R$ 0,00 — nenhum serviço mapeado para o tipo \"{$medicalRecord->type}\". Configure em Serviços > Mapeamento Tipos.";

        return redirect()->route('invoices.show', $invoice)->with('success', $msg);
    }

    protected function getTypeLabel($type): string
    {
        $labels = [
            'consulta' => 'Consulta',
            'cirurgia' => 'Cirurgia',
            'emergencia' => 'Emergência',
            'vacina' => 'Vacina',
            'retorno' => 'Retorno',
            'exame' => 'Exame',
            'consultation' => 'Consulta',
        ];
        return $labels[$type] ?? ucfirst($type);
    }

    protected function getVeterinarians()
    {
        $vetRole = Role::where('slug', 'veterinario')->first();
        if (!$vetRole) {
            return collect();
        }
        return User::where('role_id', $vetRole->id)->where('is_active', true)->orderBy('name')->get();
    }
}
