<?php

declare(strict_types=1);

return [
    'credentials' => storage_path('app/firebase/firebase_credentials.json'),

    'database' => [
        'url' => 'https://ciats-9545b-default-rtdb.asia-southeast1.firebasedatabase.app/    '
    ],

    'auth' => [
        'emulator_host' => env('FIREBASE_AUTH_EMULATOR_HOST'),
    ],
];

