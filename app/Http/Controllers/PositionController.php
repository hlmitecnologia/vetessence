<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:positions.view')->only(['index', 'show']);
        $this->middleware('can:positions.create')->only(['create', 'store']);
        $this->middleware('can:positions.edit')->only(['edit', 'update']);
        $this->middleware('can:positions.delete')->only(['destroy']);
    }

    public function index()
    {
        $positions = Position::with('department')->orderBy('name')->get();
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return redirect()->route('positions.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')->with('success', 'Cargo cadastrado com sucesso!');
    }

    public function show(Position $position)
    {
        $position->load('department', 'users');
        return view('positions.show', compact('position'));
    }

    public function edit($position)
    {
        return redirect()->route('positions.index');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    public function destroy(Position $position)
    {
        if ($position->users()->count() > 0) {
            return back()->with('error', 'Remova os usuários vinculados antes de excluir.');
        }
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Cargo excluído.');
    }
}
