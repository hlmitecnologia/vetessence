<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnesthesiaMonitoring extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'surgery_id', 'pet_id', 'vet_id', 'anesthetist',
        'anesthetic_protocol', 'premedication', 'induction_agent', 'maintenance_agent',
        'iv_access', 'intubation_type', 'monitoring_start', 'monitoring_end',
        'fluid_type', 'fluid_rate', 'observations', 'branch_id',
    ];

    protected $casts = [
        'monitoring_start' => 'datetime',
        'monitoring_end' => 'datetime',
    ];

    public function surgery(): BelongsTo { return $this->belongsTo(Surgery::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function vitalSigns(): HasMany { return $this->hasMany(AnesthesiaVitalSign::class); }
}
