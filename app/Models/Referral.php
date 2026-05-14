<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_number', 'pet_id', 'referring_vet_id', 'referring_clinic',
        'receiving_vet_id', 'receiving_clinic', 'appointment_id',
        'reason', 'clinical_history', 'requested_procedures',
        'attachments', 'status', 'response_notes', 'completed_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'completed_at' => 'date',
    ];

    public static function generateNumber(): string
    {
        return 'REF-' . date('Ymd') . '-' . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function referringVet(): BelongsTo { return $this->belongsTo(User::class, 'referring_vet_id'); }
    public function receivingVet(): BelongsTo { return $this->belongsTo(User::class, 'receiving_vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
}
