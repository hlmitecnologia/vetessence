<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'work_date', 'start_time', 'end_time',
        'shift_type', 'notes', 'created_by',
        'is_on_call', 'on_call_type', 'branch_id',
        'is_vet_shift',
    ];

    protected $casts = [
        'work_date' => 'date',
        'is_on_call' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
