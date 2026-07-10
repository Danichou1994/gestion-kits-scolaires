<?php

return [
    'name' => 'gestion-kits-scolaires',

    'source' => [
        'files' => [
            'include' => [
                base_path('storage/app'),
                base_path('public'),
            ],
            'exclude' => [
                base_path('storage/app/temp'),
                base_path('storage/app/google'),
            ],
        ],

        'databases' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'gestion_kits'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'dump' => [
            'use_single_transaction' => true,
            'timeout' => 60,
        ],
    ],
],
    ],

    'destination' => [
        'filename_prefix' => 'backup-',
        'disk' => env('BACKUP_DISK', 'local'),
    ],
];