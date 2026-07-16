<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvenioCoveredPet extends Model
{
    use HasFactory;

    protected $table = 'convenio_covered_pets';

    protected $fillable = [
        'subscription_id', 'pet_id',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ConvenioSubscription::class, 'subscription_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
