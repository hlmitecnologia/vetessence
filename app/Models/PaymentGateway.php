<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'provider', 'is_active', 'is_sandbox',
        'public_key', 'secret_key', 'webhook_secret',
        'webhook_url', 'config', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'config' => 'array',
    ];

    protected $hidden = ['secret_key', 'webhook_secret'];

    public function scopeActive($query) { return $query->where('is_active', true); }
}
