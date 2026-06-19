<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:departments.view')->only(['index', 'show']);
        $this->middleware('can:departments.create')->only(['create', 'store']);
        $this->middleware('can:departments.edit')->only(['edit', 'update']);
        $this->middleware('can:departments.delete')->only(['destroy']);
    }

    public function index()
    {
        $departments = Department::withCount('positions')->orderBy('name')->paginate(20);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return redirect()->route('departments.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Departamento cadastrado com sucesso!');
    }

    public function show(Department $department)
    {
        $department->load('positions');
        return view('departments.show', compact('department'));
    }

    public function edit($department)
    {
        return redirect()->route('departments.index');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Departamento atualizado com sucesso!');
    }

    public function destroy(Department $department)
    {
        if ($department->positions()->count() > 0) {
            return back()->with('error', 'Remova os cargos vinculados antes de excluir.');
        }
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Remova os usuários vinculados antes de excluir.');
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departamento excluído.');
    }
}
