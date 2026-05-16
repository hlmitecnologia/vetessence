<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmergencyProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'species', 'severity', 'description',
        'procedure_steps', 'medications', 'category', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($p) {
            $p->slug = $p->slug ?: Str::slug($p->title);
        });
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeForSpecies($q, $s) { return $s ? $q->where('species', $s) : $q; }
}
