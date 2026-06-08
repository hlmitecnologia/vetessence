<?php

return [
    'focusnfe' => [
        'base_url' => env('FOCUSNFE_BASE_URL', 'https://api.focusnfe.com.br'),
    ],
    'nfeio' => [
        'base_url' => env('NFEIO_BASE_URL', 'https://api.nfe.io'),
    ],
    'webmania' => [
        'base_url' => env('WEBMANIA_BASE_URL', 'https://api.webmania.com.br/v1'),
    ],
    'ambiente' => env('NFE_AMBIENTE', 'homologacao'),
];
