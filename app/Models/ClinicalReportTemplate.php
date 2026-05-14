<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'species', 'specialty', 'category',
        'description', 'content', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeForSpecies($query, $species)
    {
        return $query->where(function ($q) use ($species) {
            $q->where('species', $species)->orWhereNull('species');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
