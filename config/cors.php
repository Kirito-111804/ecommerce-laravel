<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all methods (GET, POST, PUT, DELETE)
    
    'allowed_origins' => ['http://localhost:3000'], // Replace with your React app's URL

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // If your requests need cookies or auth headers
];
