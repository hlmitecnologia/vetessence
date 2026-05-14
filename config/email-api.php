<?php

return [
    'url' => env('EMAIL_API_URL', 'https://api.example.com/send'),
    'token' => env('EMAIL_API_TOKEN', ''),
    'timeout' => env('EMAIL_API_TIMEOUT', 15),
];
