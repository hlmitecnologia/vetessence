<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Pet;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:diet-plans.view')->only(['index', 'show']);
        $this->middleware('can:diet-plans.create')->only(['create', 'store']);
        $this->middleware('can:diet-plans.edit')->only(['edit', 'update']);
        $this->middleware('can:diet-plans.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = DietPlan::with(['pet', 'createdBy']);
        if ($request->pet_id) $query->where('pet_id', $request->pet_id);
        if ($request->diet_type) $query->where('diet_type', $request->diet_type);
        $plans = $query->latest()->paginate(20);
        return view('diet-plans.index', compact('plans'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('diet-plans.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'diet_type' => 'required|string|max:50',
            'brand' => 'nullable|string|max:100',
            'product_name' => 'nullable|string|max:200',
            'daily_amount' => 'nullable|string|max:100',
            'duration_days' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['created_by'] = auth()->id();
        DietPlan::create($data);
        return redirect()->route('diet-plans.index')
            ->with('success', 'Plano alimentar cadastrado.');
    }

    public function show(DietPlan $dietPlan)
    {
        $dietPlan->load(['pet', 'medicalRecord', 'createdBy']);
        return view('diet-plans.show', compact('dietPlan'));
    }

    public function edit(DietPlan $dietPlan)
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('diet-plans.edit', compact('dietPlan', 'pets'));
    }

    public function update(Request $request, DietPlan $dietPlan)
    {
        $data = $request->validate([
            'diet_type' => 'required|string|max:50',
            'brand' => 'nullable|string|max:100',
            'product_name' => 'nullable|string|max:200',
            'daily_amount' => 'nullable|string|max:100',
            'duration_days' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $dietPlan->update($data);
        return redirect()->route('diet-plans.index')
            ->with('success', 'Plano alimentar atualizado.');
    }

    public function destroy(DietPlan $dietPlan)
    {
        $dietPlan->delete();
        return redirect()->route('diet-plans.index')
            ->with('success', 'Plano alimentar removido.');
    }
}
