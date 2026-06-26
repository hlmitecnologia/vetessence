<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeForm extends Component
{
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $phone = '';
    public $role_id = '';
    public $branch_id = '';
    public $is_active = true;
    public $is_veterinarian = false;
    public $department_id = '';
    public $position_id = '';
    public $hire_date = '';
    public $contract_type = '';

    public $roles = [];
    public $branches = [];
    public $departments = [];
    public $positions = [];
    public $contractTypes = [];

    public function mount($id = null)
    {
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
        $this->positions = Position::orderBy('name')->pluck('name', 'id');
        $this->contractTypes = config('hr.contract_types', []);
        if ($id) $this->load($id);
    }

    public function updatedDepartmentId($value)
    {
        $this->position_id = '';
        if ($value) {
            $this->positions = Position::where('department_id', $value)->orderBy('name')->pluck('name', 'id');
        } else {
            $this->positions = Position::orderBy('name')->pluck('name', 'id');
        }
    }

    #[On('editUser')]
    public function load($id)
    {
        $this->userId = $id;
        $user = User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role_id = (string) ($user->role_id ?? '');
        $this->branch_id = (string) ($user->branch_id ?? '');
        $this->is_active = $user->is_active;
        $this->is_veterinarian = $user->is_veterinarian;
        $this->department_id = (string) ($user->department_id ?? '');
        $this->position_id = (string) ($user->position_id ?? '');
        $this->hire_date = $user->hire_date?->format('Y-m-d') ?? '';
        $this->contract_type = $user->contract_type ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
        $this->contractTypes = config('hr.contract_types', []);
        $this->updatedDepartmentId($this->department_id);
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->phone = '';
        $this->role_id = '';
        $this->branch_id = '';
        $this->is_active = true;
        $this->is_veterinarian = false;
        $this->department_id = '';
        $this->position_id = '';
        $this->hire_date = '';
        $this->contract_type = '';
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->pluck('name', 'id');
        $this->positions = Position::orderBy('name')->pluck('name', 'id');
        $this->contractTypes = config('hr.contract_types', []);
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->userId ?: 'NULL'),
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
            'is_veterinarian' => 'boolean',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|string|max:30',
        ];

        $canManageSecurity = auth()->user()->can('users.create');

        if ($this->userId) {
            if ($this->password) {
                $rules['password'] = 'min:8|confirmed';
            }
        } elseif ($canManageSecurity) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        $this->phone = $this->phone ?: null;
        $this->is_active = (bool) $this->is_active;
        $this->is_veterinarian = (bool) $this->is_veterinarian;

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'branch_id' => $this->branch_id ?: null,
            'is_active' => $this->is_active,
            'is_veterinarian' => $this->is_veterinarian,
            'department_id' => $this->department_id ?: null,
            'position_id' => $this->position_id ?: null,
            'hire_date' => $this->hire_date ?: null,
            'contract_type' => $this->contract_type ?: null,
        ];

        if ($canManageSecurity && $this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            if ($canManageSecurity && $this->role_id) {
                $role = Role::find($this->role_id);
                if ($role) {
                    $user->role_id = $role->id;
                    $user->save();
                    $user->syncRoles([$role->name]);
                }
            }
            $this->dispatch('user-saved');
        } else {
            if (!$canManageSecurity) {
                $data['password'] = bcrypt(Str::random(32));
            }
            $user = User::create($data);
            if ($canManageSecurity && $this->role_id) {
                $role = Role::find($this->role_id);
                if ($role) {
                    $user->role_id = $role->id;
                    $user->save();
                    $user->assignRole($role->name);
                }
            }
            if (!$canManageSecurity) {
                Password::sendResetLink(['email' => $user->email]);
            }
            $this->dispatch('user-saved');
        }

        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.employee-form');
    }
}
