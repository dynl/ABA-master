<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'user*', 'vaccines/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173', // Your Vue App
    ],

    // THIS IS THE FIX FOR FLUTTER WEB
    // It allows http://localhost:ANY_PORT and http://127.0.0.1:ANY_PORT
    'allowed_origins_patterns' => [
        '/http:\/\/localhost:\d+/',
        '/http:\/\/127\.0\.0\.1:\d+/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
