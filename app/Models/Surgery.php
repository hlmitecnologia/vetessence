<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'vet_id', 'medical_record_id', 'scheduled_date',
        'surgery_type', 'status', 'anesthesia_type', 'protocol',
        'pre_op_diagnosis', 'post_op_notes', 'surgery_duration', 'complications', 'notes'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'surgery_duration' => 'integer',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
