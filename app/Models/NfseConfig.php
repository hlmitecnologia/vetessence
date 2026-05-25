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
        'webmania_app_id',
        'webmania_app_secret',
        'webmania_consumer_key',
        'webmania_consumer_secret',
        'focusnfe_token',
        'spedy_api_key',
        'spedy_api_secret',
        'tecnospeed_token',
        'nfeio_api_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
