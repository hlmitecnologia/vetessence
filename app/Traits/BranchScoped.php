<?php

namespace App\Traits;

use App\Models\Branch;
use App\Scopes\BranchScope;
use App\Services\BranchContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BranchScoped
{
    public static function bootBranchScoped(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            if ($model->branch_id === null && BranchContext::hasBranch()) {
                $model->branch_id = BranchContext::get();
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForBranch($query, ?int $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }

    public function scopeWithoutBranch($query)
    {
        return $query->withoutGlobalScope(BranchScope::class);
    }
}
