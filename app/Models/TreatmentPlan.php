<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BranchScoped;
use Illuminate\Support\Str;

class TreatmentPlan extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'plan_number', 'pet_id', 'tutor_id', 'vet_id', 'appointment_id',
        'title', 'description', 'total_estimated', 'total_authorized',
        'status', 'client_approved_at', 'rejected_at', 'rejection_reason',
        'client_notes', 'vet_notes', 'branch_id',
    ];

    protected $casts = [
        'client_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_estimated' => 'decimal:2',
        'total_authorized' => 'decimal:2',
    ];

    public function scopePending($q) { return $q->where('status', 'pending_approval'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopeRejected($q) { return $q->where('status', 'rejected'); }

    public function isPending(): bool { return $this->status === 'pending_approval'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function approve(): void
    {
        $this->update(['status' => 'approved', 'client_approved_at' => now()]);
    }

    public function reject(string $reason = null): void
    {
        $this->update(['status' => 'rejected', 'rejected_at' => now(), 'rejection_reason' => $reason]);
    }

    public static function generateNumber(): string
    {
        $prefix = 'PLN-' . date('Y') . '-';
        $last = self::where('plan_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
        $next = $last ? (int) substr($last->plan_number, -5) + 1 : 1;
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function tutor(): BelongsTo { return $this->belongsTo(Tutor::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function items(): HasMany { return $this->hasMany(TreatmentPlanItem::class); }
}
