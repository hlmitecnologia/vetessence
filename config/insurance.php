<?php

return [
    'porto_seguro' => [
        'url' => env('PORTO_SEGURO_API_URL', 'https://api.portoseguro.com.br/v1/claims'),
        'key' => env('PORTO_SEGURO_API_KEY', ''),
        'timeout' => env('PORTO_SEGURO_TIMEOUT', 30),
    ],
];
