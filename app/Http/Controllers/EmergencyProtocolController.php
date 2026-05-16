<?php

namespace App\Http\Controllers;

use App\Models\EmergencyProtocol;
use Illuminate\Http\Request;

class EmergencyProtocolController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:emergency-protocols.view')->only(['index', 'show']);
        $this->middleware('can:emergency-protocols.create')->only(['create', 'store']);
        $this->middleware('can:emergency-protocols.edit')->only(['edit', 'update']);
        $this->middleware('can:emergency-protocols.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = EmergencyProtocol::query();

        if ($request->species) {
            $query->where('species', $request->species);
        }
        if ($request->severity) {
            $query->where('severity', $request->severity);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $protocols = $query->orderBy('title')->paginate(20);
        $speciesList = EmergencyProtocol::select('species')->distinct()->pluck('species')->filter();

        return view('emergency-protocols.index', compact('protocols', 'speciesList'));
    }

    public function create()
    {
        return view('emergency-protocols.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'species' => 'nullable|string|max:50',
            'severity' => 'required|in:critical,urgent,stable',
            'description' => 'nullable|string',
            'procedure_steps' => 'required|string',
            'medications' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        EmergencyProtocol::create($data);
        return redirect()->route('emergency-protocols.index')->with('success', 'Protocolo cadastrado.');
    }

    public function show(EmergencyProtocol $emergencyProtocol)
    {
        return view('emergency-protocols.show', compact('emergencyProtocol'));
    }

    public function edit(EmergencyProtocol $emergencyProtocol)
    {
        return view('emergency-protocols.edit', compact('emergencyProtocol'));
    }

    public function update(Request $request, EmergencyProtocol $emergencyProtocol)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'species' => 'nullable|string|max:50',
            'severity' => 'required|in:critical,urgent,stable',
            'description' => 'nullable|string',
            'procedure_steps' => 'required|string',
            'medications' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $emergencyProtocol->update($data);
        return redirect()->route('emergency-protocols.index')->with('success', 'Protocolo atualizado.');
    }

    public function destroy(EmergencyProtocol $emergencyProtocol)
    {
        $emergencyProtocol->delete();
        return redirect()->route('emergency-protocols.index')->with('success', 'Protocolo excluído.');
    }
}
