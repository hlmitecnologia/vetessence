<?php

return [
    'webmania' => [
        'base_url' => env('WEBMANIA_BASE_URL', 'https://api.webmania.com.br'),
    ],

    'focusnfe' => [
        'base_url' => env('FOCUSNFE_BASE_URL', 'https://api.focusnfe.com.br'),
    ],

    'spedy' => [
        'base_url' => env('SPEDY_BASE_URL', 'https://api.spedy.com.br'),
    ],

    'nfeio' => [
        'base_url' => env('NFEIO_BASE_URL', 'https://api.nfe.io'),
    ],

    'ambiente' => env('NFSE_AMBIENTE', 'homologacao'),
];
