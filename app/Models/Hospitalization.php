<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospitalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'tutor_id', 'vet_id', 'appointment_id',
        'admission_date', 'admission_time', 'admission_reason', 'initial_diagnosis',
        'department', 'bed', 'is_emergency', 'status',
        'discharged_at', 'discharge_summary', 'discharge_instructions',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'discharged_at' => 'date',
        'is_emergency' => 'boolean',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function tutor(): BelongsTo { return $this->belongsTo(Tutor::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function dailyRecords(): HasMany { return $this->hasMany(HospitalizationDailyRecord::class); }
    public function fluidTherapies(): HasMany { return $this->hasMany(HospitalizationFluidTherapy::class); }
    public function prescriptions(): HasMany { return $this->hasMany(HospitalizationPrescription::class); }
}
