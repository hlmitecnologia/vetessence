<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    private function groupedPermissions(): array
    {
        $permissions = Permission::orderBy('name')->get();
        $grouped = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name, 2);
            $group = $parts[0] ?? 'outros';
            $grouped[$group][] = $perm;
        }
        return $grouped;
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = $this->groupedPermissions();
        return view('roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:Spatie\Permission\Models\Permission,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web',
        ]);

        $spatieRole = SpatieRole::findOrCreate($validated['slug'], 'web');
        $spatieRole->syncPermissions($request->permissions ?? []);

        $role->spatiePermissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Perfil cadastrado!');
    }

    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $groupedPermissions = $this->groupedPermissions();
        $role->load('spatiePermissions');
        return view('roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:Spatie\Permission\Models\Permission,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        try {
            $spatieRole = SpatieRole::findByName($role->slug, 'web');
            $spatieRole->syncPermissions($request->permissions ?? []);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // Spatie role will be created on next sync
        }

        $role->spatiePermissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Perfil possui usuários vinculados.');
        }

        try {
            $spatieRole = SpatieRole::findByName($role->slug, 'web');
            $spatieRole->delete();
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // already gone
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Perfil excluído!');
    }
}
