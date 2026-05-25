<?php

return [
    'webmania' => [
        'base_url' => env('WEBMANIA_BASE_URL', 'https://api.webmania.com.br/v1'),
    ],

    'focusnfe' => [
        'base_url' => env('FOCUSNFE_BASE_URL', 'https://api.focusnfe.com.br'),
    ],

    'ginfes' => [
        'base_url' => env('GINFES_BASE_URL', 'https://api.ginfes.com.br'),
    ],

    'ambiente' => env('NFSE_AMBIENTE', 'homologacao'),
];
