<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommissionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'commissionable_type', 'commissionable_id',
        'rate_type', 'rate_value', 'is_active',
    ];

    protected $casts = [
        'rate_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commissionable(): MorphTo
    {
        return $this->morphTo();
    }
}
