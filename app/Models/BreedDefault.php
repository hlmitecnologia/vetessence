<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreedDefault extends Model
{
    use HasFactory;
    protected $fillable = [
        'species', 'breed', 'size', 'avg_weight_min', 'avg_weight_max',
        'avg_lifespan_min', 'avg_lifespan_max', 'temperament',
        'predispositions', 'notes', 'is_active',
    ];

    protected $casts = [
        'avg_weight_min' => 'decimal:2',
        'avg_weight_max' => 'decimal:2',
        'avg_lifespan_min' => 'integer',
        'avg_lifespan_max' => 'integer',
        'is_active' => 'boolean',
    ];
}
