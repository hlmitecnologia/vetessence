<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationQueue extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'tutor_id', 'pet_id', 'template_id', 'channel', 'destination',
        'message_content', 'scheduled_at', 'sent_at', 'status', 'error_message', 'branch_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function tutor(): BelongsTo { return $this->belongsTo(Tutor::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function template(): BelongsTo { return $this->belongsTo(CommunicationTemplate::class, 'template_id'); }
}
