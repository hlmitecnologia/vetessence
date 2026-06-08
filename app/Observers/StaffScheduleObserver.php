<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\StaffSchedule;
use App\Services\VetAvailabilityService;

class StaffScheduleObserver
{
    public function deleted(StaffSchedule $schedule)
    {
        if (!$schedule->is_vet_shift) {
            return;
        }

        $this->cancelFutureAppointments($schedule->user_id, $schedule->work_date);
    }

    public function updated(StaffSchedule $schedule)
    {
        if (!$schedule->is_vet_shift) {
            return;
        }

        $dirty = $schedule->getDirty();
        if (isset($dirty['work_date']) || isset($dirty['start_time']) || isset($dirty['end_time'])) {
            $this->cancelFutureAppointments($schedule->user_id, $schedule->work_date);
        }
    }

    protected function cancelFutureAppointments(int $vetId, string $date)
    {
        $appointments = Appointment::where('vet_id', $vetId)
            ->whereDate('date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();

        $availability = app(VetAvailabilityService::class);

        foreach ($appointments as $appointment) {
            $stillAvailable = $availability->isSlotAvailable(
                $vetId,
                $appointment->date->toDateString(),
                $appointment->time->format('H:i'),
                $appointment->duration ?? 30
            );

            if (!$stillAvailable) {
                $appointment->update([
                    'status' => 'cancelled',
                    'reason' => ($appointment->reason ? $appointment->reason . ' | ' : '') . 'Cancelado automaticamente: alteração na escala do veterinário.',
                ]);
            }
        }
    }
}
