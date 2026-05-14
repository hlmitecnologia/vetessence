<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_plan_id', 'description', 'category',
        'quantity', 'unit_price', 'total', 'is_authorized', 'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'is_authorized' => 'boolean',
    ];

    public function treatmentPlan(): BelongsTo { return $this->belongsTo(TreatmentPlan::class); }
}
