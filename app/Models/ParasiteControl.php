<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParasiteControl extends Model
{
    use HasFactory;

    public function scopeOverdue($query)
    {
        return $query->where('next_due_date', '<', now());
    }

    protected $fillable = [
        'pet_id', 'product_name', 'active_ingredient', 'type',
        'application_date', 'next_due_date', 'dose', 'batch',
        'vet_id', 'medical_record_id', 'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function medicalRecord(): BelongsTo { return $this->belongsTo(MedicalRecord::class); }
}
