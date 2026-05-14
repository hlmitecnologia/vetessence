<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    use HasFactory, HasPhoto;

    protected $fillable = [
        'name', 'species', 'breed', 'gender', 'birth_date', 'weight',
        'color', 'microchip', 'photo', 'coat', 'size', 'is_active', 'notes',
        'created_at_branch_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tutors(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'pet_tutor')
            ->withPivot('is_primary', 'relationship');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function convenioPets(): HasMany
    {
        return $this->hasMany(ConvenioPet::class);
    }

    public function createdAtBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'created_at_branch_id');
    }

    public function getAgeMonthsAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->diffInMonths(now()) : 0;
    }

    public function getAgeAttribute(): ?string
    {
        if (!$this->birth_date) return null;

        $years = $this->birth_date->age;
        $months = $this->birth_date->diffInMonths(now());

        if ($years > 0) {
            return $years . ' ano(s)';
        }
        return $months . ' mes(es)';
    }
}
