<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccineProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'species', 'vaccine_name', 'age_start_weeks', 'age_end_weeks',
        'is_initial', 'dose_number', 'booster_interval_months',
        'is_core', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_initial' => 'boolean',
        'is_core' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeForSpecies($query, $species)
    {
        return $query->where('species', $species)->where('is_active', true);
    }

    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    public static function suggestForPet(Pet $pet, ?int $ageWeeks = null)
    {
        if (!$ageWeeks) {
            $ageWeeks = $pet->birth_date ? $pet->birth_date->diffInWeeks(now()) : null;
        }
        if (!$ageWeeks) {
            return collect();
        }

        return static::forSpecies($pet->species)
            ->where(function ($q) use ($ageWeeks) {
                $q->whereNull('age_start_weeks')
                  ->orWhere('age_start_weeks', '<=', $ageWeeks);
            })
            ->where(function ($q) use ($ageWeeks) {
                $q->whereNull('age_end_weeks')
                  ->orWhere('age_end_weeks', '>=', $ageWeeks);
            })
            ->orderBy('is_core', 'desc')
            ->orderBy('age_start_weeks')
            ->get();
    }
}
