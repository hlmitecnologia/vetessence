<?php

return [
    'focusnfe' => [
        'base_url' => env('FOCUSNFE_BASE_URL', 'https://api.focusnfe.com.br'),
    ],
    'nfeio' => [
        'base_url' => env('NFEIO_BASE_URL', 'https://api.nfse.io'),
    ],
    'webmania' => [
        'base_url' => env('WEBMANIA_BASE_URL', 'https://webmania.com.br/api/1'),
    ],
    'ambiente' => env('NFE_AMBIENTE', 'homologacao'),
];
