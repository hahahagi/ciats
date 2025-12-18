<?php

return [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    
    // PAKAI ABSOLUTE PATH dengan base_path()
    'credentials' => base_path('storage/app/firebase_credentials.json'),
    
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
    
    'auth' => [
        'emulator_host' => env('FIREBASE_AUTH_EMULATOR_HOST'),
    ],
];