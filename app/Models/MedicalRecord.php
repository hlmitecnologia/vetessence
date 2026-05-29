<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BranchScoped;
use App\Traits\DigitalSignable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory, BranchScoped, DigitalSignable;

    protected $fillable = [
        'record_id', 'version', 'pet_id', 'appointment_id', 'user_id',
        'date', 'time', 'type', 'chief_complaint', 'anamnesis',
        'physical_exam', 'vital_signs', 'diagnosis', 'treatment',
        'prognosis', 'attachments', 'notes', 'content_hash',
        'digital_signature', 'signed_at', 'branch_id'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'vital_signs' => 'array',
        'attachments' => 'array',
        'signed_at' => 'datetime',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function parentRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'record_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'record_id');
    }

    public function zoonoticDiseases(): BelongsToMany
    {
        return $this->belongsToMany(ZoonoticDisease::class, 'diagnosis_disease')
            ->withPivot('is_suspected', 'notes');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function hasGeneratedPaidInvoice(): bool
    {
        if ($this->appointment && $this->appointment->hasPaidInvoice()) {
            return true;
        }
        return $this->invoices()->where('status', 'paid')->exists();
    }
}
