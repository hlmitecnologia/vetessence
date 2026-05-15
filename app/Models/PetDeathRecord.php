<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetDeathRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'death_date', 'cause', 'attending_vet',
        'notes', 'disposition', 'cremation_type',
        'cremation_pickup_date', 'cremation_notes', 'memorial_text',
        'registered_by', 'authorized_by', 'authorization_doc',
    ];

    protected $casts = [
        'death_date' => 'date',
        'cremation_pickup_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
