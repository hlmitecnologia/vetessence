<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'tutor_id', 'type', 'channel', 'destination', 'sent_at', 'status', 'message', 'error_message'];

    protected $casts = ['sent_at' => 'datetime'];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function tutor(): BelongsTo { return $this->belongsTo(Tutor::class); }
}
