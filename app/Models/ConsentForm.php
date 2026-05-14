<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConsentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'consent_number', 'pet_id', 'tutor_id', 'appointment_id', 'consent_template_id',
        'signed_content', 'client_name', 'client_document', 'veterinarian_id', 'witness_id',
        'signed_at', 'signature_data', 'status', 'notes',
    ];

    protected $casts = ['signed_at' => 'datetime'];

    public static function generateNumber(): string
    {
        return 'CON-' . date('Ymd') . '-' . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function tutor(): BelongsTo { return $this->belongsTo(Tutor::class); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function template(): BelongsTo { return $this->belongsTo(ConsentTemplate::class, 'consent_template_id'); }
    public function veterinarian(): BelongsTo { return $this->belongsTo(User::class, 'veterinarian_id'); }
    public function witness(): BelongsTo { return $this->belongsTo(User::class, 'witness_id'); }
}
