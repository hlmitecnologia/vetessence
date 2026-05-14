<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroomingTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'species', 'breed', 'size', 'services',
        'price', 'estimated_minutes', 'notes', 'is_active',
    ];

    protected $casts = [
        'services' => 'array',
        'price' => 'decimal:2',
        'estimated_minutes' => 'integer',
        'is_active' => 'boolean',
    ];
}
