<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET, POST, PUT, DELETE, OPTIONS'],
    'allowed_origins' => ['http://localhost:5173'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type, Authorization', 'X-Requested-With','X-Tenant-Token'],
    'exposed_headers' => ['*'],
    'max_age' => 0,
    'supports_credentials' => false,
];