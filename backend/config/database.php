<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [

        // Runtime connection — Supabase's transaction-mode pooler (port 6543).
        // pgbouncer transaction mode does not support server-side prepared
        // statement caching (each statement can land on a different backend
        // connection), so PDO::ATTR_EMULATE_PREPARES must be forced on here
        // or queries intermittently fail with "prepared statement already
        // exists" under load. See backend_details.md §2.
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '6543'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'require'),
            'options' => [
                PDO::ATTR_EMULATE_PREPARES => true,
            ],
        ],

        // Migration-only connection — Supabase's session-mode pooler (port
        // 5432). Needed for full session semantics (multi-statement
        // transactions, DDL). Never used for normal app runtime queries.
        // Migration classes opt into this via `protected $connection`.
        'pgsql_migrations' => [
            'driver' => 'pgsql',
            'url' => env('DIRECT_URL'),
            'host' => env('DB_MIGRATIONS_HOST', '127.0.0.1'),
            'port' => env('DB_MIGRATIONS_PORT', '5432'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'require'),
        ],

    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

    ],

];
