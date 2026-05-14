<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class HospitalizationPrescription extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'hospitalization_id', 'medication', 'dosage', 'unit', 'frequency',
        'route', 'start_date', 'end_date', 'status', 'prescribed_by', 'notes', 'branch_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function hospitalization(): BelongsTo { return $this->belongsTo(Hospitalization::class); }
    public function prescribedBy(): BelongsTo { return $this->belongsTo(User::class, 'prescribed_by'); }
}
