<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlledSubstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'active_ingredient', 'schedule', 'anvisa_register',
        'unit', 'current_stock', 'min_stock', 'is_active', 'notes',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function logs(): HasMany { return $this->hasMany(ControlledSubstanceLog::class); }
}
