<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringAppointments extends Command
{
    protected $signature = 'appointments:generate-recurring';
    protected $description = 'Generate recurring appointments based on recurrence rules';

    public function handle()
    {
        $recurring = Appointment::where('is_recurring', true)
            ->whereNull('recurrence_end_date')
            ->orWhere('recurrence_end_date', '>=', now())
            ->get();

        $generated = 0;

        foreach ($recurring as $appointment) {
            $rule = $appointment->recurrence_rule;
            $lastGenerated = Appointment::where('parent_appointment_id', $appointment->id)
                ->max('date');

            $nextDate = $lastGenerated
                ? Carbon::parse($lastGenerated)->addDay()
                : Carbon::parse($appointment->date)->addDay();

            $endDate = $appointment->recurrence_end_date
                ? Carbon::parse($appointment->recurrence_end_date)
                : Carbon::parse($appointment->date)->addYear();

            while ($nextDate->lte($endDate) && $nextDate->lte(now()->addDays(90))) {
                $shouldGenerate = false;

                switch ($rule) {
                    case 'daily':
                        $shouldGenerate = true;
                        break;
                    case 'weekly':
                        $shouldGenerate = $nextDate->dayOfWeek === Carbon::parse($appointment->date)->dayOfWeek;
                        break;
                    case 'biweekly':
                        $shouldGenerate = $nextDate->dayOfWeek === Carbon::parse($appointment->date)->dayOfWeek
                            && $nextDate->diffInWeeks(Carbon::parse($appointment->date)) % 2 === 0;
                        break;
                    case 'monthly':
                        $shouldGenerate = $nextDate->day === Carbon::parse($appointment->date)->day;
                        break;
                }

                if ($shouldGenerate) {
                    Appointment::create([
                        'pet_id' => $appointment->pet_id,
                        'vet_id' => $appointment->vet_id,
                        'date' => $nextDate->format('Y-m-d'),
                        'time' => $appointment->time,
                        'type' => $appointment->type,
                        'status' => 'scheduled',
                        'reason' => $appointment->reason,
                        'notes' => $appointment->notes,
                        'duration' => $appointment->duration,
                        'room' => $appointment->room,
                        'parent_appointment_id' => $appointment->id,
                        'is_recurring' => false,
                    ]);
                    $generated++;
                }

                $nextDate->addDay();
            }
        }

        $this->info("Generated {$generated} recurring appointments.");
    }
}
