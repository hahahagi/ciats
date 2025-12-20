<?php

declare(strict_types=1);

return [
    'credentials' => base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase_credentials.json')),

    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],

    'auth' => [
        'emulator_host' => env('FIREBASE_AUTH_EMULATOR_HOST'),
    ],
];
