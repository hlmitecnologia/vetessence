<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use App\Traits\RetentionProtected;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlledSubstanceLog extends Model
{
    use HasFactory, BranchScoped, RetentionProtected;

    protected $fillable = [
        'controlled_substance_id', 'user_id', 'pet_id', 'type',
        'quantity', 'balance_before', 'balance_after', 'reason',
        'prescription_id', 'witness_id', 'notes', 'retention_until',
        'is_archived', 'branch_id',
    ];

    protected $casts = [
        'retention_until' => 'date',
        'is_archived' => 'boolean',
    ];

    public function substance(): BelongsTo { return $this->belongsTo(ControlledSubstance::class, 'controlled_substance_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function prescription(): BelongsTo { return $this->belongsTo(Prescription::class); }
    public function witness(): BelongsTo { return $this->belongsTo(User::class, 'witness_id'); }
}
