<?php

namespace App\Listeners;

use App\Events\AppointmentCompleted;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceFromAppointment
{
    public function handle(AppointmentCompleted $event)
    {
        $appointment = $event->appointment;
        $pet = $appointment->pet;
        $tutor = $pet->tutors()->wherePivot('is_primary', true)->first();

        if (!$tutor) {
            $tutor = $pet->tutors()->first();
        }

        if (!$tutor) {
            return;
        }

        $invoice = Invoice::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'user_id' => $appointment->vet_id,
            'branch_id' => $appointment->branch_id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'total' => 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => "Consulta: {$appointment->type}",
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ]);
    }
}
