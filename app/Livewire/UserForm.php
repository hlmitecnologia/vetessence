<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UserForm extends Component
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

    public $roles = [];
    public $branches = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'phone' => 'nullable|string|max:20',
        'role_id' => 'nullable|exists:roles,id',
        'branch_id' => 'nullable|exists:branches,id',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
        if ($id) $this->load($id);
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
        $this->password = '';
        $this->password_confirmation = '';
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
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
        $this->roles = Role::orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
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
        ];

        if ($this->userId) {
            if ($this->password) {
                $rules['password'] = 'min:8|confirmed';
            }
        } else {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        $this->phone = $this->phone ?: null;
        $this->is_active = (bool) $this->is_active;

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'branch_id' => $this->branch_id ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            if ($this->role_id) {
                $role = Role::find($this->role_id);
                if ($role) {
                    $user->role_id = $role->id;
                    $user->save();
                    $user->syncRoles([$role->name]);
                }
            }
        } else {
            $data['password'] = Hash::make($this->password);
            $user = User::create($data);
            if ($this->role_id) {
                $role = Role::find($this->role_id);
                if ($role) {
                    $user->role_id = $role->id;
                    $user->save();
                    $user->assignRole($role->name);
                }
            }
        }

        $this->dispatch('user-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.user-form');
    }
}
