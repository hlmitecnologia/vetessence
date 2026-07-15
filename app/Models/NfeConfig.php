<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider', 'ambiente',
        'focusnfe_token', 'nfeio_api_key', 'nfeio_company_id',
        'webmania_consumer_key', 'webmania_consumer_secret',
        'webmania_access_token', 'webmania_access_token_secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
