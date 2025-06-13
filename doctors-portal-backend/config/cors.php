<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'email/verify/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // or ['http://localhost:5173'] for more control

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
