<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\ConsentLoggable;
use App\Traits\HasPhoto;

class Tutor extends Authenticatable
{
    use HasFactory, HasPhoto, Notifiable, ConsentLoggable;

    protected $fillable = [
        'name', 'user_id', 'cpf', 'rg', 'phone', 'phone_secondary', 'email',
        'zipcode', 'address', 'number', 'complement', 'neighborhood',
        'city', 'state', 'profession', 'photo', 'notes',
        'password', 'remember_token',
        'created_at_branch_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['name'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : ($this->email ?? 'Tutor');
    }

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

    public function createdAtBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'created_at_branch_id');
    }
}
