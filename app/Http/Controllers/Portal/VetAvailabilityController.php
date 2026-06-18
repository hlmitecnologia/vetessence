<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\VetAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VetAvailabilityController extends Controller
{
    public function __construct(
        protected VetAvailabilityService $availabilityService
    ) {}

    public function availableVets(Request $request)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $vets = $this->availabilityService->getAvailableVets($request->date);

        return response()->json([
            'vets' => $vets->map(function ($vet) {
                return [
                    'id' => $vet->id,
                    'name' => $vet->name,
                    'crmv' => $vet->crmv,
                ];
            }),
        ]);
    }

    public function vetSlots(Request $request)
    {
        $request->validate([
            'vet_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->availabilityService->getSlotsForVet($request->vet_id, $request->date);

        return response()->json(['slots' => $slots]);
    }

    public function vetDates(Request $request)
    {
        $request->validate(['vet_id' => 'required|exists:users,id']);

        $vet = User::find($request->vet_id);
        if (!$vet || !$vet->where('is_active', true)->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))->exists()) {
            return response()->json(['dates' => []]);
        }

        $start = today();
        $end = today()->addDays(60);

        $shifts = $this->availabilityService->getVetShiftsForPeriod($vet->id, $start->toDateString(), $end->toDateString());

        $datesWithSlots = [];
        foreach ($shifts as $shift) {
            $dateStr = $shift->work_date->toDateString();
            $slots = $this->availabilityService->getSlotsForVet($vet->id, $dateStr);
            if (!empty($slots)) {
                $datesWithSlots[] = [
                    'date' => $dateStr,
                    'label' => $shift->work_date->format('d/m/Y'),
                    'day_name' => $shift->work_date->translatedFormat('l'),
                    'slots_count' => count($slots),
                ];
            }
        }

        return response()->json(['dates' => $datesWithSlots]);
    }
}
