<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfseConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'ambiente',
        'webmania_consumer_key',
        'webmania_consumer_secret',
        'webmania_access_token',
        'focusnfe_token',
        'spedy_api_key',
        'spedy_api_secret',
        'nfeio_api_key', 'nfeio_company_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
