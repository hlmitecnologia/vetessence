<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExecutionMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospitalization_id',
        'date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(Hospitalization::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ExecutionTask::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
