<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;

class ConvenioController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:convenios');
    }

    public function index(Request $request)
    {
        $query = Convenio::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        $convenios = $query->orderBy('name')->paginate(20);

        return view('convenios.index', compact('convenios'));
    }

    public function create()
    {
        return redirect()->route('convenios.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'plan_name' => 'nullable|string|max:100',
            'coverage' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'max_consults_month' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Convenio::create($validated);

        return redirect()->route('convenios.index')->with('success', 'Convênio cadastrado!');
    }

    public function show(Convenio $convenio)
    {
        $convenio->load('convenioPets.pet.tutors');
        return view('convenios.show', compact('convenio'));
    }

    public function edit($convenio)
    {
        return redirect()->route('convenios.index');
    }

    public function update(Request $request, Convenio $convenio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'plan_name' => 'nullable|string|max:100',
            'coverage' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'max_consults_month' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $convenio->update($validated);

        return redirect()->route('convenios.index')->with('success', 'Convênio atualizado!');
    }

    public function destroy(Convenio $convenio)
    {
        if ($convenio->convenioPets()->count() > 0) {
            return back()->with('error', 'Convênio possui vínculos ativos.');
        }

        $convenio->delete();
        return redirect()->route('convenios.index')->with('success', 'Convênio excluído!');
    }
}
