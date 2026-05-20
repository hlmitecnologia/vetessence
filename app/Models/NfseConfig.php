<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NfseConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'cnpj',
        'municipio_ibge',
        'regime_tributario',
        'serie',
        'ambiente',
        'webmania_app_id',
        'webmania_app_secret',
        'webmania_consumer_key',
        'webmania_consumer_secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function nfseInvoices(): HasMany
    {
        return $this->hasMany(NfseInvoice::class);
    }
}
