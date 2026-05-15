<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;

class TriageRecord extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'pet_id', 'check_in_at', 'severity', 'chief_complaint',
        'vital_signs', 'assigned_vet_id', 'triage_vet_id',
        'status', 'seen_at', 'discharged_at', 'branch_id',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'vital_signs' => 'array',
        'seen_at' => 'datetime',
        'discharged_at' => 'datetime',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function assignedVet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_vet_id');
    }

    public function triageVet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triage_vet_id');
    }
}
