<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'type', 'check_in_at', 'expected_check_out',
        'check_out_at', 'status', 'daily_rate', 'grooming_fee',
        'total_amount', 'reason', 'feeding_instructions',
        'medication_instructions', 'pickup_contact', 'notes',
        'created_by', 'checked_out_by',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'expected_check_out' => 'datetime',
        'check_out_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'grooming_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function checkedOutBy(): BelongsTo { return $this->belongsTo(User::class, 'checked_out_by'); }
    public function dailyTasks(): HasMany { return $this->hasMany(BoardingDailyTask::class); }

    public function scopeActive($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function daysBoarded(): int
    {
        $end = $this->check_out_at ?? now();
        return max(1, (int) $this->check_in_at->diffInDays($end) + 1);
    }

    public function calculateTotal(): void
    {
        $this->total_amount = ($this->daily_rate * $this->daysBoarded()) + $this->grooming_fee;
    }
}
