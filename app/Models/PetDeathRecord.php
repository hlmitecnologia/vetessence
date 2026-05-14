<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetDeathRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_id', 'death_date', 'cause', 'attending_vet',
        'notes', 'disposition', 'registered_by',
    ];

    protected $casts = [
        'death_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
