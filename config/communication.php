<?php

return [
    'whatsapp' => [
        'url' => env('WHATSAPP_API_URL', 'https://api.z-api.io/v1'),
        'token' => env('WHATSAPP_API_TOKEN', ''),
        'instance' => env('WHATSAPP_INSTANCE', ''),
    ],

    'sms' => [
        'url' => env('SMS_API_URL', 'https://api.smsprovider.com/v1/send'),
        'key' => env('SMS_API_KEY', ''),
    ],
];
