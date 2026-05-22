<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id', 'name', 'ibge_code',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function scopeByState($query, $stateId)
    {
        return $query->where('state_id', $stateId);
    }
}
