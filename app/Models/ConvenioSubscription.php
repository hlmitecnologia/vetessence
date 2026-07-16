<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConvenioSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'convenio_id', 'policy_number', 'discount_percent',
        'start_date', 'end_date', 'is_active',
        'external_policy_id', 'eligibility_last_checked_at',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'eligibility_last_checked_at' => 'datetime',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function coveredPets(): HasMany
    {
        return $this->hasMany(ConvenioCoveredPet::class, 'subscription_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(ConvenioClaim::class, 'subscription_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }
}
