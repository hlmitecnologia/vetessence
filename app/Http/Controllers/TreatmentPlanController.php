<?php

namespace App\Http\Controllers;

use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentPlanController extends Controller
{
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

        $plans = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('treatment-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('treatment-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'required|exists:tutors,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_estimated' => 'nullable|numeric|min:0',
            'total_authorized' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
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
        return view('treatment-plans.edit', compact('treatmentPlan'));
    }

    public function update(Request $request, TreatmentPlan $treatmentPlan)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'required|exists:tutors,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_estimated' => 'nullable|numeric|min:0',
            'total_authorized' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
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
}
