<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapySession extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_id', 'type', 'session_date', 'therapist_id',
        'duration_minutes', 'notes', 'observations', 'status',
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
}
