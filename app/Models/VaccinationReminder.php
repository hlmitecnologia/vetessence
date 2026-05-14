<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationReminder extends Model
{
    use HasFactory;

    protected $fillable = ['vaccination_id', 'pet_id', 'scheduled_date', 'sent_at', 'channel', 'status', 'error_message'];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function vaccination(): BelongsTo { return $this->belongsTo(Vaccination::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
}
