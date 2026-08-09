<?php

return [

    // Randomly generated signing secret — never commit a real value.
    // Generate one with: php -r "echo bin2hex(random_bytes(32));"
    'secret' => env('JWT_SECRET'),

    'algo' => 'HS256',

    // Minutes until an issued token expires.
    'ttl' => (int) env('JWT_TTL', 720),

];
