<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider', 'ambiente',
        'focusnfe_token', 'nfeio_api_key',
        'webmania_app_id', 'webmania_app_secret',
        'webmania_consumer_key', 'webmania_consumer_secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
