<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory, BranchScoped {
        BranchScoped::bootBranchScoped as private bootBranchScopedTrait;
    }

    protected static function bootBranchScoped(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }

    protected $fillable = [
        'name', 'provider', 'channel', 'is_active', 'is_sandbox',
        'public_key', 'secret_key', 'webhook_secret',
        'webhook_url', 'config', 'notes', 'branch_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'config' => 'array',
    ];

    protected $hidden = ['secret_key', 'webhook_secret'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByChannel($query, string $channel)
    {
        if ($channel === 'portal') {
            return $query->whereIn('channel', ['portal', 'both']);
        }
        return $query->where('channel', $channel);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'gateway_id');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withoutBranch()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    public function isPortal(): bool
    {
        return in_array($this->channel, ['portal', 'both']);
    }

    public function isPdv(): bool
    {
        return false;
    }
}
