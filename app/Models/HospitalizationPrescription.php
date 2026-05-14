<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class HospitalizationPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospitalization_id', 'medication', 'dosage', 'unit', 'frequency',
        'route', 'start_date', 'end_date', 'status', 'prescribed_by', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function hospitalization(): BelongsTo { return $this->belongsTo(Hospitalization::class); }
    public function prescribedBy(): BelongsTo { return $this->belongsTo(User::class, 'prescribed_by'); }
}
