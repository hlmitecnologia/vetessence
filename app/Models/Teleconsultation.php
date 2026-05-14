<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Teleconsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name', 'room_token', 'appointment_id', 'pet_id',
        'tutor_id', 'vet_id', 'status', 'provider',
        'provider_room_id', 'provider_url', 'scheduled_at',
        'started_at', 'ended_at', 'duration_minutes',
        'notes', 'recording_url',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function tutor(): BelongsTo { return $this->belongsTo(User::class, 'tutor_id'); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }

    public static function generateRoomToken(): string
    {
        return strtoupper(Str::random(7) . '-' . Str::random(4) . '-' . Str::random(4));
    }

    public function getRoomUrlAttribute(): string
    {
        return $this->provider_url ?? url("/teleconsultation/{$this->room_token}");
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeScheduled($query) { return $query->where('status', 'scheduled'); }
}
