<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Update Configuration
    |--------------------------------------------------------------------------
    |
    | Define valores padrão para o mecanismo de auto-update.
    | As variáveis de ambiente (env) são lidas primeiro.
    | Caso não existam, usa os defaults abaixo.
    | O repositório e branch podem ser sobrescritos via banco (settings)
    | para permitir que o cliente altere sem SSH.
    |
    */
    'repo' => env('GITHUB_REPO', 'hlmitecnologia/vetessence'),
    'branch' => env('GITHUB_BRANCH', 'main'),
    'token' => env('GITHUB_TOKEN'),
];
