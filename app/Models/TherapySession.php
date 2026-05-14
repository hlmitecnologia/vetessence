<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapySession extends Model
{
    use HasFactory, BranchScoped;
    protected $fillable = [
        'pet_id', 'type', 'session_date', 'therapist_id',
        'duration_minutes', 'notes', 'observations', 'status', 'branch_id',
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
