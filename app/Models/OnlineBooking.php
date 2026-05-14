<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineBooking extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'tutor_name', 'tutor_email', 'tutor_phone',
        'pet_name', 'pet_species', 'pet_breed',
        'preferred_date', 'preferred_time', 'reason', 'notes',
        'status', 'converted_appointment_id', 'staff_notes',
        'handled_by', 'handled_at', 'branch_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'handled_at', 'branch_id' => 'datetime',
    ];

    public function convertedAppointment(): BelongsTo { return $this->belongsTo(Appointment::class, 'converted_appointment_id'); }
    public function handledBy(): BelongsTo { return $this->belongsTo(User::class, 'handled_by'); }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
}
