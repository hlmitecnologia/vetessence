<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugFormulary extends Model
{
    use HasFactory;

    protected $table = 'drug_formulary';

    protected $fillable = [
        'drug', 'species', 'dosage_mg_kg', 'max_dose', 'route', 'notes', 'is_active',
    ];

    protected $casts = [
        'dosage_mg_kg' => 'decimal:2',
        'max_dose' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeForSpecies($q, $species) { return $q->where('species', $species); }
    public function scopeActive($q) { return $q->where('is_active', true); }

    public static function calculateDose($drugId, $weightKg, $species)
    {
        $entry = static::where('id', $drugId)->where('species', $species)->first();
        if (!$entry) return null;

        $dose = $weightKg * $entry->dosage_mg_kg;
        if ($entry->max_dose && $dose > $entry->max_dose) {
            $dose = $entry->max_dose;
        }

        return [
            'drug' => $entry->drug,
            'species' => $entry->species,
            'weight_kg' => $weightKg,
            'dosage_mg_kg' => $entry->dosage_mg_kg,
            'calculated_dose_mg' => round($dose, 2),
            'max_dose' => $entry->max_dose,
            'route' => $entry->route,
            'notes' => $entry->notes,
        ];
    }
}
