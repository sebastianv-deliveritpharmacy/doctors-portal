<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'email/verify/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173', 'https://your-vue-app.com'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
