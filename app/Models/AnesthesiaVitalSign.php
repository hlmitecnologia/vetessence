<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnesthesiaVitalSign extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'anesthesia_monitoring_id', 'recorded_at',
        'heart_rate', 'respiratory_rate', 'spo2', 'etco2',
        'blood_pressure_systolic', 'blood_pressure_diastolic', 'blood_pressure_mean',
        'temperature', 'anesthetic_depth', 'vaporizer_setting', 'observations', 'branch_id',
    ];

    protected $casts = ['recorded_at' => 'datetime'];

    public function anesthesiaMonitoring(): BelongsTo { return $this->belongsTo(AnesthesiaMonitoring::class); }
}
