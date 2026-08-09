<?php

// This application does not use Laravel's built-in session/guard auth —
// authentication is 100% custom JWT, issued and verified in
// App\Services\JwtService and App\Http\Middleware\Authenticate. This file
// exists only because some framework internals expect config('auth.*') to
// be present; the values below are unused defaults, not active guards.

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'admins',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt-custom',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
