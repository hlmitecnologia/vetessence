<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetShopPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'name', 'description', 'type', 'services',
        'total_price', 'original_price', 'validity_days', 'max_uses', 'is_active',
    ];

    protected $casts = [
        'services' => 'array',
        'total_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(PetShopSubscription::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
