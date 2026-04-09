<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvenioPet extends Model
{
    use HasFactory;

    protected $table = 'convenio_pets';

    protected $fillable = [
        'tutor_id', 'pet_id', 'convenio_id', 'plan_name',
        'contract_number', 'status', 'start_date', 'end_date', 'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               (!$this->end_date || $this->end_date >= now());
    }
}
