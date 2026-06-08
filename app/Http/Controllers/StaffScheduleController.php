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
            'is_on_call' => 'nullable|boolean',
            'on_call_type' => 'nullable|string|max:30',
            'is_vet_shift' => 'nullable|boolean',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_on_call'] = $request->boolean('is_on_call');
        $data['is_vet_shift'] = $request->boolean('is_vet_shift');
        $data['branch_id'] = \App\Services\BranchContext::hasBranch() ? \App\Services\BranchContext::get() : null;

        $conflict = $this->detectConflict($data['user_id'], $data['work_date'], $data['start_time'], $data['end_time']);
        if ($conflict) {
            return back()->with('error', 'Conflito de horário: ' . $conflict->user->name . ' já tem escala das ' .
                $conflict->start_time . ' às ' . $conflict->end_time . '.')->withInput();
        }

        StaffSchedule::create($data);

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
            'is_on_call' => 'nullable|boolean',
            'on_call_type' => 'nullable|string|max:30',
            'is_vet_shift' => 'nullable|boolean',
        ]);

        $data['is_on_call'] = $request->boolean('is_on_call');
        $data['is_vet_shift'] = $request->boolean('is_vet_shift');
        $data['branch_id'] = \App\Services\BranchContext::hasBranch() ? \App\Services\BranchContext::get() : null;

        $conflict = $this->detectConflict($data['user_id'], $data['work_date'], $data['start_time'], $data['end_time'], $staffSchedule->id);
        if ($conflict) {
            return back()->with('error', 'Conflito de horário: ' . $conflict->user->name . ' já tem escala das ' .
                $conflict->start_time . ' às ' . $conflict->end_time . '.')->withInput();
        }

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

    public function onCallCalendar(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = \Carbon\Carbon::parse($month . '-01');
        $end = $start->copy()->endOfMonth();

        $schedules = StaffSchedule::with('user')
            ->whereBetween('work_date', [$start, $end])
            ->where('is_on_call', true)
            ->orderBy('work_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('work_date');

        return view('staff-schedules.on-call-calendar', compact('schedules', 'month', 'start', 'end'));
    }

    protected function detectConflict($userId, $workDate, $startTime, $endTime, $excludeId = null)
    {
        $query = StaffSchedule::where('user_id', $userId)
            ->where('work_date', $workDate)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $directConflict = $query->first();

        if ($directConflict) {
            return $directConflict;
        }

        return $this->detectDisplacementConflict($userId, $workDate, $startTime, $endTime, $excludeId);
    }

    protected function detectDisplacementConflict($userId, $workDate, $startTime, $endTime, $excludeId = null)
    {
        $startCarbon = \Carbon\Carbon::parse($workDate . ' ' . $startTime);
        $endCarbon = \Carbon\Carbon::parse($workDate . ' ' . $endTime);
        $minGapMinutes = 120;

        $query = StaffSchedule::where('user_id', $userId)
            ->where('work_date', $workDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $daySchedules = $query->get();

        foreach ($daySchedules as $schedule) {
            $existingStart = \Carbon\Carbon::parse($schedule->work_date . ' ' . $schedule->start_time);
            $existingEnd = \Carbon\Carbon::parse($schedule->work_date . ' ' . $schedule->end_time);

            if ($startCarbon->between($existingStart, $existingEnd) || $endCarbon->between($existingStart, $existingEnd)) {
                continue;
            }

            $gapAfterStart = $startCarbon->diffInMinutes($existingEnd, false);
            $gapBeforeEnd = $endCarbon->diffInMinutes($existingStart, false);

            if ($gapAfterStart > 0 && $gapAfterStart < $minGapMinutes) {
                return $schedule;
            }

            if ($gapBeforeEnd > 0 && $gapBeforeEnd < $minGapMinutes) {
                return $schedule;
            }
        }

        return null;
    }

    public function vetShifts()
    {
        $schedules = StaffSchedule::with('user', 'branch')
            ->where('is_vet_shift', true)
            ->orderBy('work_date', 'desc')
            ->paginate(20);

        return view('staff-schedules.vet-shifts', compact('schedules'));
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
