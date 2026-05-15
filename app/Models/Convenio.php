<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convenio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cnpj', 'plan_name', 'coverage', 'discount_percent',
        'max_consults_month', 'contract_number', 'start_date', 'end_date', 'is_active',
        'phone', 'email', 'notes'
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function convenioPets(): HasMany
    {
        return $this->hasMany(ConvenioPet::class);
    }
}
