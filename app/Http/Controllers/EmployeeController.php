<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:employees.view')->only(['index', 'show']);
        $this->middleware('can:employees.create')->only(['create']);
        $this->middleware('can:employees.edit')->only(['edit']);
    }

    public function index(Request $request)
    {
        $query = User::with(['role', 'department', 'position', 'branch']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $employees = $query->orderBy('name')->get();

        $departments = Department::orderBy('name')->pluck('name', 'id');
        $positions = Position::orderBy('name')->pluck('name', 'id');
        $branches = Branch::orderBy('name')->pluck('name', 'id');
        $contractTypes = config('hr.contract_types', []);

        return view('employees.index', compact('employees', 'departments', 'positions', 'branches', 'contractTypes'));
    }

    public function create()
    {
        return redirect()->route('employees.index');
    }

    public function edit(User $employee)
    {
        return redirect()->route('employees.index');
    }

    public function show(User $employee)
    {
        $employee->load(['role', 'department', 'position', 'branch']);
        $contractTypes = config('hr.contract_types', []);
        return view('employees.show', compact('employee', 'contractTypes'));
    }
}
