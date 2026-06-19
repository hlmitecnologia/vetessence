<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'execution_task_id',
        'performed_at',
        'performed_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ExecutionTask::class, 'execution_task_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
