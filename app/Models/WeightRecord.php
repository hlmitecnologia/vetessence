<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightRecord extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = ['pet_id', 'weight', 'bcs', 'measurement_date', 'measured_by', 'notes', 'branch_id'];

    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'decimal:2',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function measuredBy(): BelongsTo { return $this->belongsTo(User::class, 'measured_by'); }
}
