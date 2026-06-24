<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetShopSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'package_id', 'branch_id', 'start_date', 'end_date',
        'remaining_uses', 'total_uses', 'total_savings', 'status',
        'recurrence_rule', 'auto_renew',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'total_savings' => 'decimal:2',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(PetShopPackage::class, 'package_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(PetShopConsumption::class, 'subscription_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
