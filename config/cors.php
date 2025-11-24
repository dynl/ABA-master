<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'user*'],

    'allowed_methods' => ['*'],

    // Keep this empty or specific static URLs
    'allowed_origins' => [
        'http://localhost:5173', // Your Vue App
    ],

    // --- ADD THIS TO FIX FLUTTER WEB ---
    // This allows http://localhost:ANY_PORT
    'allowed_origins_patterns' => [
        '/http:\/\/localhost:\d+/',
        '/http:\/\/127\.0\.0\.1:\d+/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
