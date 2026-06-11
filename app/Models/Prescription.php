<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use App\Traits\DigitalSignable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory, BranchScoped, DigitalSignable;

    protected $fillable = [
        'medical_record_id', 'medication', 'dosage', 'unit',
        'frequency', 'duration', 'route', 'instructions', 'batch',
        'created_by', 'notes', 'verification_hash', 'verified_at',
        'content_hash', 'digital_signature', 'signed_at', 'branch_id'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($prescription) {
            if (empty($prescription->verification_hash)) {
                $raw = $prescription->medication . '|' . ($prescription->dosage ?? '') . '|' . now()->toDateTimeString() . '|' . uniqid();
                $prescription->verification_hash = hash('sha256', $raw);
            }
        });
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function controlledSubstanceLogs(): HasMany
    {
        return $this->hasMany(ControlledSubstanceLog::class, 'prescription_id');
    }

    public function getIsControlledAttribute(): bool
    {
        return $this->controlledSubstanceLogs()->exists();
    }

    public function getVerifyUrlAttribute(): string
    {
        return url("/r/{$this->verification_hash}");
    }
}
