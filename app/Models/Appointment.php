<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'pet_id', 'vet_id', 'date', 'time', 'duration', 'type',
        'status', 'reason', 'notes', 'room', 'created_by',
        'is_recurring', 'recurrence_rule', 'recurrence_end_date', 'parent_appointment_id',
        'branch_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'is_recurring' => 'boolean',
        'recurrence_end_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function exam(): HasOne
    {
        return $this->hasOne(Exam::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'appointment_invoice')
            ->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'parent_appointment_id');
    }

    public function hasPaidInvoice(): bool
    {
        return $this->invoices()->where('status', 'paid')->exists();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Appointment::class, 'parent_appointment_id');
    }

    public function getTotalAttribute(): float
    {
        return $this->services->sum(function ($service) {
            return ($service->price * $service->quantity) - $service->discount;
        });
    }
}
