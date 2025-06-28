<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the CORS settings for your Laravel backend.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:4200',
        'http://localhost:8100',
        'https://1bf3-36-70-25-94.ngrok-free.app',
        '*.ngrok-free.app',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Authorization', 'Content-Disposition'],
    'max_age' => 0,
    'supports_credentials' => true,



];
