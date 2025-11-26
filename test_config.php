<?php

require_once 'vendor/autoload.php';

// Create a simple env function for testing
function env($key, $default = null)
{
    // For testing purposes, we'll just return the default value
    return $default;
}

// Test each configuration file
$configFiles = [
    'app' => 'config/app.php',
    'auth' => 'config/auth.php',
    'cache' => 'config/cache.php',
    'database' => 'config/database.php',
    'session' => 'config/session.php',
    'queue' => 'config/queue.php',
    'sanctum' => 'config/sanctum.php',
];

foreach ($configFiles as $name => $file) {
    echo "Testing $name config...\n";
    $result = require $file;
    echo "Type: " . gettype($result) . "\n";
    if (is_array($result)) {
        echo "Array size: " . count($result) . "\n";
    } else {
        echo "ERROR: Expected array, got " . gettype($result) . "\n";
    }
    echo "---\n";
}
