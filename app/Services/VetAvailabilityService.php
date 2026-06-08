<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\StaffSchedule;
use App\Models\StaffTimeOff;
use App\Models\User;
use Carbon\Carbon;

class VetAvailabilityService
{
    public function getAvailableVets(string $date)
    {
        $vetRole = Role::where('slug', 'veterinario')->first();
        if (!$vetRole) {
            return collect();
        }

        $vets = User::where('role_id', $vetRole->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return $vets->filter(function ($vet) use ($date) {
            return $this->hasShiftOnDate($vet->id, $date)
                && $this->hasAvailableSlots($vet->id, $date);
        })->values();
    }

    public function getSlotsForVet(int $vetId, string $date, int $defaultDuration = 30)
    {
        $shifts = $this->getVetShifts($vetId, $date);
        if ($shifts->isEmpty()) {
            return [];
        }

        $appointments = $this->getVetAppointments($vetId, $date);
        $timeOff = $this->getApprovedTimeOff($vetId, $date);

        if ($timeOff) {
            return [];
        }

        $slots = [];
        foreach ($shifts as $shift) {
            $start = Carbon::parse($shift->work_date . ' ' . $shift->start_time);
            $end = Carbon::parse($shift->work_date . ' ' . $shift->end_time);

            while ($start->copy()->addMinutes($defaultDuration)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($defaultDuration);
                $timeStr = $start->format('H:i');

                $hasConflict = $appointments->contains(function ($apt) use ($start, $slotEnd) {
                    $aptStart = Carbon::parse($apt->date . ' ' . $apt->time);
                    $aptDuration = $apt->duration ?? 30;
                    $aptEnd = $aptStart->copy()->addMinutes($aptDuration);
                    return $start->lt($aptEnd) && $slotEnd->gt($aptStart);
                });

                if (!$hasConflict) {
                    $slots[] = [
                        'time' => $timeStr,
                        'label' => $start->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                    ];
                }

                $start->addMinutes($defaultDuration);
            }
        }

        return $slots;
    }

    public function hasShiftOnDate(int $vetId, string $date): bool
    {
        return StaffSchedule::where('user_id', $vetId)
            ->where('work_date', $date)
            ->where('is_vet_shift', true)
            ->exists();
    }

    public function hasAvailableSlots(int $vetId, string $date, int $defaultDuration = 30): bool
    {
        return !empty($this->getSlotsForVet($vetId, $date, $defaultDuration));
    }

    public function isSlotAvailable(int $vetId, string $date, string $time, int $duration = 30): bool
    {
        $slots = $this->getSlotsForVet($vetId, $date, $duration);
        return collect($slots)->contains('time', $time);
    }

    protected function getVetShifts(int $vetId, string $date)
    {
        return StaffSchedule::where('user_id', $vetId)
            ->where('work_date', $date)
            ->where('is_vet_shift', true)
            ->orderBy('start_time')
            ->get();
    }

    protected function getVetAppointments(int $vetId, string $date)
    {
        return Appointment::where('vet_id', $vetId)
            ->whereDate('date', $date)
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress', 'checked_in', 'waiting'])
            ->get();
    }

    protected function getApprovedTimeOff(int $vetId, string $date)
    {
        return StaffTimeOff::where('user_id', $vetId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    public function getVetShiftsForPeriod(int $vetId, string $startDate, string $endDate)
    {
        return StaffSchedule::where('user_id', $vetId)
            ->where('is_vet_shift', true)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->orderBy('work_date')
            ->orderBy('start_time')
            ->get();
    }
}
