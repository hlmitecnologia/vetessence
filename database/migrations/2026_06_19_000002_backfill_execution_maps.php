<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        $now = Carbon::now();
        $prescriptions = DB::table('hospitalization_prescriptions')
            ->whereDate('start_date', '<=', $now)
            ->orderBy('hospitalization_id')
            ->orderBy('start_date')
            ->get();

        foreach ($prescriptions->groupBy('hospitalization_id') as $hospId => $rxList) {
            $hospitalization = DB::table('hospitalizations')->find($hospId);
            if (!$hospitalization) continue;

            $start = Carbon::parse($rxList->min('start_date'));
            $end = $hospitalization->end_date
                ? Carbon::parse($hospitalization->end_date)
                : $now;

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $exists = DB::table('execution_maps')
                    ->where('hospitalization_id', $hospId)
                    ->where('date', $date->toDateString())
                    ->exists();
                if ($exists) continue;

                $mapId = DB::table('execution_maps')->insertGetId([
                    'hospitalization_id' => $hospId,
                    'date' => $date->toDateString(),
                    'created_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($rxList as $rx) {
                    if ($rx->end_date && $date->gt(Carbon::parse($rx->end_date))) continue;
                    if ($date->lt(Carbon::parse($rx->start_date))) continue;

                    $intervals = [
                        'every_8h' => [6, 14, 22],
                        'every_12h' => [8, 20],
                        'every_6h' => [0, 6, 12, 18],
                        'every_24h' => [8],
                    ];
                    $hours = $intervals[$rx->frequency] ?? [8];

                    foreach ($hours as $hour) {
                        DB::table('execution_tasks')->insert([
                            'execution_map_id' => $mapId,
                            'category' => 'medication',
                            'title' => $rx->medication,
                            'description' => $rx->notes ?? null,
                            'scheduled_time' => sprintf('%02d:00', $hour),
                            'frequency' => $rx->frequency,
                            'route' => $rx->route ?? null,
                            'dosage' => $rx->dosage ?? null,
                            'unit' => $rx->unit ?? null,
                            'source_type' => 'hospitalization_prescription',
                            'source_id' => $rx->id,
                            'status' => 'pending',
                            'sort_order' => 0,
                            'created_by' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    public function down()
    {
        DB::table('execution_maps')->truncate();
        DB::table('execution_tasks')->truncate();
        DB::table('execution_logs')->truncate();
    }
};
