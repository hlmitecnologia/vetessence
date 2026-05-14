<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'drug_a', 'drug_b', 'severity', 'description',
        'mechanism', 'management', 'source', 'category', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDrug($query, $drugName)
    {
        return $query->where(function ($q) use ($drugName) {
            $q->where('drug_a', $drugName)->orWhere('drug_b', $drugName);
        });
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }
}
