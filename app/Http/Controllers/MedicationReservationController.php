<?php

namespace App\Http\Controllers;

use App\Models\MedicationAdministration;
use App\Models\MedicationReservation;
use App\Models\Product;
use App\Notifications\MedicationReservationCreated;
use App\Services\FractionalInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MedicationReservationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'pet_id' => ['nullable', 'exists:pets,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', 'string', 'max:30'],
            'priority' => ['nullable', 'in:normal,urgent'],
            'source_type' => ['nullable', 'string', 'max:255'],
            'source_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'pending';
        $reservation = MedicationReservation::create($data);

        $pharmacyUsers = \App\Models\User::permission('medication-reservations.view')->get();
        Notification::send($pharmacyUsers, new MedicationReservationCreated($reservation->load('product')));

        return response()->json($reservation->load('product'), 201);
    }

    public function fulfill(Request $request, MedicationReservation $reservation, FractionalInventoryService $inventory)
    {
        $data = $request->validate(['quantity' => ['nullable', 'numeric', 'gt:0']]);
        abort_unless(in_array($reservation->status, ['pending', 'partially_separated'], true), 422, 'Reserva não está pendente.');
        $quantity = (float) ($data['quantity'] ?? $reservation->quantity);
        $product = $reservation->product;
        $inventory->consume($product, $quantity, $reservation->unit, [
            'reference' => 'reservation:' . $reservation->id,
            'reason' => 'reservation-fulfillment',
        ]);
        $reservation->update([
            'status' => $quantity < (float) $reservation->quantity ? 'partially_separated' : 'delivered',
            'fulfilled_by' => $request->user()->id,
            'fulfilled_at' => now(),
        ]);
        return response()->json($reservation->fresh());
    }

    public function administer(Request $request, MedicationReservation $reservation, FractionalInventoryService $inventory)
    {
        $data = $request->validate([
            'quantity_administered' => ['required', 'numeric', 'gt:0'],
            'route' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);
        $product = $reservation->product;
        $movement = $inventory->consume($product, (float) $data['quantity_administered'], $reservation->unit, [
            'reference' => 'reservation:' . $reservation->id,
            'reason' => 'administration',
        ]);
        $administration = DB::transaction(function () use ($request, $reservation, $data, $movement) {
            $record = MedicationAdministration::create([
                'product_id' => $reservation->product_id,
                'inventory_batch_id' => $movement->inventory_batch_id,
                'inventory_container_id' => $movement->inventory_container_id,
                'medication_reservation_id' => $reservation->id,
                'pet_id' => $reservation->pet_id,
                'prescribed_by' => $reservation->requested_by,
                'administered_by' => $request->user()->id,
                'quantity_prescribed' => $reservation->quantity,
                'quantity_administered' => $data['quantity_administered'],
                'unit' => $reservation->unit,
                'route' => $data['route'] ?? null,
                'notes' => $data['notes'] ?? null,
                'administered_at' => now(),
                'branch_id' => $reservation->branch_id,
            ]);
            $reservation->update(['status' => 'consumed']);
            return $record;
        });
        return response()->json($administration->load('product'), 201);
    }

    public function cancel(Request $request, MedicationReservation $reservation)
    {
        abort_unless(in_array($reservation->status, ['pending', 'partially_separated'], true), 422, 'Reserva não pode ser cancelada.');
        $reservation->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        return response()->json($reservation->fresh());
    }
}
