<?php

namespace App\Listeners;

use App\Events\AppointmentCompleted;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceTypeMap;

class GenerateInvoiceFromAppointment
{
    public function handle(AppointmentCompleted $event)
    {
        $appointment = $event->appointment->load(['pet.tutors', 'services.service']);

        if ($appointment->hasPaidInvoice()) {
            return;
        }

        $pet = $appointment->pet;
        $tutor = $pet->tutors()->wherePivot('is_primary', true)->first() ?? $pet->tutors()->first();

        if (!$tutor) {
            return;
        }

        $existingInvoice = Invoice::where('tutor_id', $tutor->id)
            ->where('pet_id', $pet->id)
            ->where('branch_id', $appointment->branch_id)
            ->where('status', 'pending')
            ->first();

        $appointmentServices = $appointment->services;

        if ($appointmentServices->isNotEmpty()) {
            $total = $appointmentServices->sum(fn($s) => ($s->price * $s->quantity) - $s->discount);

            if ($existingInvoice) {
                $invoice = $existingInvoice;
                $invoice->appointments()->syncWithoutDetaching([$appointment->id]);

                $newTotal = $invoice->subtotal + $total;
                $invoice->update([
                    'total' => $newTotal,
                    'subtotal' => $newTotal,
                ]);
            } else {
                $invoice = Invoice::create([
                    'pet_id' => $pet->id,
                    'tutor_id' => $tutor->id,
                    'user_id' => $appointment->vet_id,
                    'branch_id' => $appointment->branch_id,
                    'invoice_number' => Invoice::generateNumber(),
                    'status' => 'pending',
                    'total' => $total,
                    'subtotal' => $total,
                    'due_date' => $appointment->date,
                ]);
                $invoice->appointments()->attach($appointment->id);
            }

            foreach ($appointmentServices as $as) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $as->service?->name ?? "Serviço #{$as->service_id}",
                    'quantity' => $as->quantity,
                    'unit_price' => $as->price,
                    'total' => ($as->price * $as->quantity) - $as->discount,
                ]);
            }
            return;
        }

        // Fallback: usar mapeamento tipo → serviço
        $map = ServiceTypeMap::where('type', $appointment->type)
            ->where(function ($q) use ($appointment) {
                $q->whereNull('branch_id')
                  ->orWhere('branch_id', $appointment->branch_id);
            })
            ->orderBy('branch_id', 'desc')
            ->first();

        $service = $map?->service;
        $price = $service?->price ?? 0;

        if ($existingInvoice) {
            $invoice = $existingInvoice;
            $invoice->appointments()->syncWithoutDetaching([$appointment->id]);

            $newTotal = $invoice->subtotal + $price;
            $invoice->update([
                'total' => $newTotal,
                'subtotal' => $newTotal,
            ]);
        } else {
            $invoice = Invoice::create([
                'pet_id' => $pet->id,
                'tutor_id' => $tutor->id,
                'user_id' => $appointment->vet_id,
                'branch_id' => $appointment->branch_id,
                'invoice_number' => Invoice::generateNumber(),
                'status' => 'pending',
                'total' => $price,
                'subtotal' => $price,
                'due_date' => $appointment->date,
            ]);
            $invoice->appointments()->attach($appointment->id);
        }

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $service?->name ?? "Consulta: {$appointment->type}",
            'quantity' => 1,
            'unit_price' => $price,
            'total' => $price,
        ]);
    }
}
