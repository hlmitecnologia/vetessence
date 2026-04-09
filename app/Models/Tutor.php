<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPhoto;

class Tutor extends Model
{
    use HasFactory, HasPhoto;

    protected $fillable = [
        'user_id', 'cpf', 'rg', 'phone', 'phone_secondary', 'email',
        'zipcode', 'address', 'number', 'complement', 'neighborhood',
        'city', 'state', 'profession', 'photo', 'notes'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_tutor')
            ->withPivot('is_primary', 'relationship');
    }

    public function primaryPets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_tutor')
            ->wherePivot('is_primary', true);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function convenioPets(): HasMany
    {
        return $this->hasMany(ConvenioPet::class);
    }
}
