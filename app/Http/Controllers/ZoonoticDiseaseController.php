<?php

namespace App\Http\Controllers;

use App\Models\ZoonoticDisease;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ZoonoticDiseaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:prontuarios');
    }

    public function index(Request $request)
    {
        $query = ZoonoticDisease::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('causative_agent', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->is_notifiable !== null) {
            $query->where('is_notifiable', $request->boolean('is_notifiable'));
        }

        $diseases = $query->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json($diseases);
        }

        return view('zoonotic-diseases.index', compact('diseases'));
    }

    public function create()
    {
        return redirect()->route('zoonotic-diseases.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:viral,bacterial,parasitic,fungal,prion',
            'causative_agent' => 'nullable|string|max:255',
            'transmission' => 'nullable|string',
            'animal_symptoms' => 'nullable|string',
            'human_symptoms' => 'nullable|string',
            'incubation_period' => 'nullable|string|max:100',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'is_notifiable' => 'boolean',
            'species_affected' => 'nullable|array',
            'species_affected.*' => 'string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_notifiable'] = $request->boolean('is_notifiable');
        $validated['is_active'] = $request->boolean('is_active', true);

        ZoonoticDisease::create($validated);

        return redirect()->route('zoonotic-diseases.index')
            ->with('success', 'Doença zoonótica cadastrada com sucesso!');
    }

    public function show(ZoonoticDisease $zoonoticDisease)
    {
        $zoonoticDisease->load('medicalRecords.pet');
        return view('zoonotic-diseases.show', compact('zoonoticDisease'));
    }

    public function edit($zoonoticDisease)
    {
        return redirect()->route('zoonotic-diseases.index');
    }

    public function update(Request $request, ZoonoticDisease $zoonoticDisease)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:viral,bacterial,parasitic,fungal,prion',
            'causative_agent' => 'nullable|string|max:255',
            'transmission' => 'nullable|string',
            'animal_symptoms' => 'nullable|string',
            'human_symptoms' => 'nullable|string',
            'incubation_period' => 'nullable|string|max:100',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'is_notifiable' => 'boolean',
            'species_affected' => 'nullable|array',
            'species_affected.*' => 'string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_notifiable'] = $request->boolean('is_notifiable');
        $validated['is_active'] = $request->boolean('is_active', true);

        $zoonoticDisease->update($validated);

        return redirect()->route('zoonotic-diseases.index')
            ->with('success', 'Doença zoonótica atualizada com sucesso!');
    }

    public function destroy(ZoonoticDisease $zoonoticDisease)
    {
        if ($zoonoticDisease->medicalRecords()->count() > 0) {
            return back()->with('error', 'Não é possível excluir: doença vinculada a prontuários.');
        }
        $zoonoticDisease->delete();
        return redirect()->route('zoonotic-diseases.index')
            ->with('success', 'Doença zoonótica excluída!');
    }
}
