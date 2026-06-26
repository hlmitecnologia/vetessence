<?php

namespace App\Http\Controllers;

use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:plano-tratamento');
    }

    public function index(Request $request)
    {
        $query = TreatmentPlan::with(['pet', 'tutor', 'vet', 'items']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('plan_number', 'like', "%{$request->search}%")
                  ->orWhere('title', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $plans = $query->orderBy('created_at', 'desc')->get();

        return view('treatment-plans.index', compact('plans'));
    }

    public function create()
    {
        $pets = \App\Models\Pet::with('tutors')->get();
        $veterinarians = \App\Models\User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();
        return view('treatment-plans.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'nullable|exists:tutors,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_estimated' => 'nullable|numeric|min:0',
            'total_authorized' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending_approval,approved,rejected,in_progress,completed,cancelled',
            'client_notes' => 'nullable|string',
            'vet_notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string|max:255',
            'items.*.category' => 'nullable|string|max:100',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.is_authorized' => 'boolean',
            'items.*.notes' => 'nullable|string',
        ]);

        if (!$validated['tutor_id']) {
            $pet = \App\Models\Pet::with('tutors')->find($validated['pet_id']);
            $validated['tutor_id'] = $pet?->tutors->first()?->id;
        }

        DB::beginTransaction();
        try {
            $validated['plan_number'] = TreatmentPlan::generateNumber();
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $plan = TreatmentPlan::create($validated);

            foreach ($items as $itemData) {
                $itemData['treatment_plan_id'] = $plan->id;
                $itemData['total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                $itemData['is_authorized'] = $itemData['is_authorized'] ?? false;
                TreatmentPlanItem::create($itemData);
            }

            DB::commit();
            return redirect()->route('treatment-plans.index')->with('success', 'Plano de tratamento criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar plano de tratamento.')->withInput();
        }
    }

    public function show(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->load(['pet', 'tutor', 'vet', 'appointment', 'items']);

        $plan = $treatmentPlan;
        return view('treatment-plans.show', compact('plan'));
    }

    public function edit(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->load('items');
        $plan = $treatmentPlan;
        $pets = \App\Models\Pet::with('tutors')->get();
        $veterinarians = \App\Models\User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();
        return view('treatment-plans.edit', compact('plan', 'pets', 'veterinarians'));
    }

    public function update(Request $request, TreatmentPlan $treatmentPlan)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'nullable|exists:tutors,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_estimated' => 'nullable|numeric|min:0',
            'total_authorized' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending_approval,approved,rejected,in_progress,completed,cancelled',
            'client_approved_at' => 'nullable|date',
            'client_notes' => 'nullable|string',
            'vet_notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string|max:255',
            'items.*.category' => 'nullable|string|max:100',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.is_authorized' => 'boolean',
            'items.*.notes' => 'nullable|string',
            'items.*.id' => 'nullable|exists:treatment_plan_items,id',
        ]);

        if (!$validated['tutor_id']) {
            $pet = \App\Models\Pet::with('tutors')->find($validated['pet_id']);
            $validated['tutor_id'] = $pet?->tutors->first()?->id;
        }

        DB::beginTransaction();
        try {
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $treatmentPlan->update($validated);

            $existingIds = [];
            foreach ($items as $itemData) {
                $itemData['total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                $itemData['is_authorized'] = $itemData['is_authorized'] ?? false;

                if (isset($itemData['id'])) {
                    TreatmentPlanItem::where('id', $itemData['id'])
                        ->where('treatment_plan_id', $treatmentPlan->id)
                        ->update($itemData);
                    $existingIds[] = $itemData['id'];
                } else {
                    $itemData['treatment_plan_id'] = $treatmentPlan->id;
                    $item = TreatmentPlanItem::create($itemData);
                    $existingIds[] = $item->id;
                }
            }

            TreatmentPlanItem::where('treatment_plan_id', $treatmentPlan->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();
            return redirect()->route('treatment-plans.index')->with('success', 'Plano de tratamento atualizado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar plano de tratamento.')->withInput();
        }
    }

    public function destroy(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->items()->delete();
        $treatmentPlan->delete();

        return redirect()->route('treatment-plans.index')->with('success', 'Plano de tratamento excluído!');
    }

    public function approve(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->approve();
        return redirect()->route('treatment-plans.show', $treatmentPlan)
            ->with('success', 'Orçamento aprovado pelo tutor.');
    }

    public function reject(Request $request, TreatmentPlan $treatmentPlan)
    {
        $data = $request->validate(['rejection_reason' => 'nullable|string|max:1000']);
        $treatmentPlan->reject($data['rejection_reason'] ?? null);
        return redirect()->route('treatment-plans.show', $treatmentPlan)
            ->with('error', 'Orçamento recusado.');
    }
}
