<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConsentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'consentable_type', 'consentable_id', 'user_id', 'type',
        'purpose', 'granted', 'ip_address', 'user_agent', 'consented_at',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'consented_at' => 'datetime',
    ];

    public function consentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
