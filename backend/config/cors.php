<?php

return [

    'paths' => [
        'api/*',
        'login',
        'logout',
        'register',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://pars-experimo.ir',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ],

    // اجازه دسترسی خودکار به تمام آی‌پی‌های شبکه محلی (وای‌فای) روی پورت 5173
    'allowed_origins_patterns' => [
        '#^http://192\.168\.\d+\.\d+:5173$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
