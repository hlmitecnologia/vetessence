<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardingDailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'boarding_id', 'task_date', 'task_name', 'description',
        'is_completed', 'completed_at', 'completed_by', 'observations',
    ];

    protected $casts = [
        'task_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function boarding(): BelongsTo { return $this->belongsTo(Boarding::class); }
    public function completedBy(): BelongsTo { return $this->belongsTo(User::class, 'completed_by'); }
}
