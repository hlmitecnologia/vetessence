<?php

namespace App\Http\Controllers;

use App\Models\StaffSchedule;
use App\Models\StaffTimeOff;
use App\Models\User;
use Illuminate\Http\Request;

class StaffScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:agenda-equipe');
    }

    public function index()
    {
        $schedules = StaffSchedule::with('user')->orderBy('work_date', 'desc')->paginate(20);
        return view('staff-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('staff-schedules.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'shift_type' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();

        StaffSchedule::updateOrCreate(
            ['user_id' => $data['user_id'], 'work_date' => $data['work_date']],
            $data
        );

        return redirect()->route('staff-schedules.index')
            ->with('success', 'Escala registrada.');
    }

    public function edit(StaffSchedule $staffSchedule)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('staff-schedules.edit', compact('staffSchedule', 'users'));
    }

    public function update(Request $request, StaffSchedule $staffSchedule)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'shift_type' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $staffSchedule->update($data);

        return redirect()->route('staff-schedules.index')
            ->with('success', 'Escala atualizada.');
    }

    public function destroy(StaffSchedule $staffSchedule)
    {
        $staffSchedule->delete();
        return redirect()->route('staff-schedules.index')
            ->with('success', 'Escala removida.');
    }

    public function timeOff()
    {
        $timeOffs = StaffTimeOff::with(['user', 'approvedBy'])->latest()->paginate(20);
        return view('staff-schedules.time-off', compact('timeOffs'));
    }

    public function storeTimeOff(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|max:50',
            'reason' => 'nullable|string|max:255',
        ]);

        $data['status'] = 'pending';

        StaffTimeOff::create($data);

        return redirect()->route('staff-schedules.time-off')
            ->with('success', 'Solicitação de folga registrada.');
    }

    public function approveTimeOff(StaffTimeOff $staffTimeOff)
    {
        $staffTimeOff->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Folga aprovada.');
    }

    public function rejectTimeOff(StaffTimeOff $staffTimeOff)
    {
        $staffTimeOff->update(['status' => 'rejected']);
        return back()->with('success', 'Folga rejeitada.');
    }
}
