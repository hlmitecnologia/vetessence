<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'address', 'number', 'neighborhood', 'complement',
        'city', 'state',
        'zip_code', 'phone', 'email', 'cnpj',
        'municipio_ibge', 'regime_tributario', 'serie',
        'is_active', 'is_main', 'notes',
        'state_id', 'city_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main' => 'boolean',
    ];

    public function users(): HasMany { return $this->hasMany(User::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeMain($query) { return $query->where('is_main', true); }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
