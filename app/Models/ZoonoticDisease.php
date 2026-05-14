<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ZoonoticDisease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'category', 'causative_agent', 'transmission',
        'animal_symptoms', 'human_symptoms', 'incubation_period',
        'prevention', 'treatment', 'is_notifiable', 'species_affected',
        'notes', 'is_active',
    ];

    protected $casts = [
        'is_notifiable' => 'boolean',
        'is_active' => 'boolean',
        'species_affected' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($disease) {
            if (empty($disease->slug)) {
                $disease->slug = Str::slug($disease->name);
            }
        });
    }

    public function medicalRecords(): BelongsToMany
    {
        return $this->belongsToMany(MedicalRecord::class, 'diagnosis_disease')
            ->withPivot('is_suspected', 'notes');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotifiable($query)
    {
        return $query->where('is_notifiable', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            'viral' => 'Viral',
            'bacterial' => 'Bacteriana',
            'parasitic' => 'Parasitária',
            'fungal' => 'Fúngica',
            'prion' => 'Prion',
        ];
        return $labels[$this->category] ?? $this->category;
    }
}
