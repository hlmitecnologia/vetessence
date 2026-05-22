<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'uf', 'ibge_code', 'country',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function scopeByUf($query, $uf)
    {
        return $query->where('uf', strtoupper($uf));
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', strtoupper($country));
    }
}
