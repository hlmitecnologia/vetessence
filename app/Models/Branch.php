<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'address', 'city', 'state',
        'zip_code', 'phone', 'email', 'cnpj',
        'is_active', 'is_main', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main' => 'boolean',
    ];

    public function users(): HasMany { return $this->hasMany(User::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeMain($query) { return $query->where('is_main', true); }
}
