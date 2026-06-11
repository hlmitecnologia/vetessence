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
        'state_id', 'city_id',
        'notify_sms', 'notify_whatsapp', 'notify_email',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['name'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notify_sms' => 'boolean',
        'notify_whatsapp' => 'boolean',
        'notify_email' => 'boolean',
    ];

    public function getNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        if ($this->attributes['name'] ?? null) {
            return $this->attributes['name'];
        }
        return $this->email ?? 'Tutor';
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
