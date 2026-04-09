<?php

return [
    'pix_key' => env('PIX_KEY', 'admin@vetessence.com'),
    'gi' => env('PIX_GI', 'br.gov.bcb.pix'),
    'merchant_name' => env('PIX_MERCHANT_NAME', 'VETESSENCE CLINICA VETERINARIA'),
    'city' => env('PIX_CITY', 'SAO PAULO'),
    'url' => env('PIX_URL', ''),
    'is_unique_payment' => env('PIX_IS_UNIQUE_PAYMENT', false),
    'description' => 'Pagamento VetEssence',
];
