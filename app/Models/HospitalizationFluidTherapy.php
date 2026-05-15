<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalizationFluidTherapy extends Model
{
    use HasFactory, BranchScoped;

    protected $table = 'hospitalization_fluid_therapy';

    protected $fillable = [
        'hospitalization_id', 'fluid_type', 'rate', 'volume',
        'start_time', 'end_time', 'route', 'observations', 'branch_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function hospitalization(): BelongsTo { return $this->belongsTo(Hospitalization::class); }
}
