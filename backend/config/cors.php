<?php

return [

    // The frontend is static HTML with no fixed origin — it may be opened
    // via file://, or served from any local dev port, or eventually hosted
    // somewhere. backend_details.md §7 explicitly allows "*" for this reason.
    // Once the frontend has a real deployed origin, tighten this to that
    // exact origin list instead of "*".
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
