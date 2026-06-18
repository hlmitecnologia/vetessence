<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $validated['permissions'] = $request->permissions ?? [];
        $validated['permissions'] = json_encode($validated['permissions']);

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Perfil cadastrado!');
    }

    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $validated['permissions'] = $request->permissions ?? [];
        $validated['permissions'] = json_encode($validated['permissions']);

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Perfil possui usuários vinculados.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Perfil excluído!');
    }
}
