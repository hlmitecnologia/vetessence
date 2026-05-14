<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'medical_record_id', 'medication', 'dosage', 'unit',
        'frequency', 'duration', 'route', 'instructions', 'batch',
        'created_by', 'notes', 'branch_id'
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
