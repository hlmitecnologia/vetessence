<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffNote extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'title', 'content', 'priority', 'created_by',
        'assigned_to', 'category', 'is_read', 'read_at', 'branch_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at', 'branch_id' => 'datetime',
    ];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at', 'branch_id' => now()]);
    }
}
