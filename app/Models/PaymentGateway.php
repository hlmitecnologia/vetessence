<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGateway extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'name', 'provider', 'is_active', 'is_sandbox',
        'public_key', 'secret_key', 'webhook_secret',
        'webhook_url', 'config', 'notes', 'branch_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'config' => 'array',
    ];

    protected $hidden = ['secret_key', 'webhook_secret'];

    public function scopeActive($query) { return $query->where('is_active', true); }
}
