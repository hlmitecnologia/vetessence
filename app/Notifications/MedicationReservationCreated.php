<?php

namespace App\Notifications;

use App\Models\MedicationReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class MedicationReservationCreated extends Notification
{
    use Queueable;

    public function __construct(public MedicationReservation $reservation) {}

    public function via($notifiable): array { return ['database']; }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'medication_reservation',
            'reservation_id' => $this->reservation->id,
            'title' => 'Nova reserva de medicamento',
            'message' => sprintf('%s %s %s solicitado para a farmácia.', $this->reservation->quantity, $this->reservation->unit, $this->reservation->product->name),
            'priority' => $this->reservation->priority,
        ]);
    }
}
