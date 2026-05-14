<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetTutor extends Model
{
    use HasFactory;

    protected $table = 'pet_tutor';

    protected $fillable = [
        'pet_id', 'tutor_id', 'is_primary', 'relationship',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}
