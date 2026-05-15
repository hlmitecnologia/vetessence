<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;

class PreAnestheticEvaluation extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'pet_id', 'surgery_id', 'medical_record_id', 'vet_id',
        'asa_score', 'fasted', 'hydrated', 'exam_checklist',
        'observations', 'status', 'recommendations', 'branch_id',
    ];

    protected $casts = [
        'fasted' => 'boolean',
        'hydrated' => 'boolean',
        'exam_checklist' => 'array',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function surgery(): BelongsTo
    {
        return $this->belongsTo(Surgery::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
