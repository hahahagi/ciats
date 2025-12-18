<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;

class FirebaseAdminSeeder extends Seeder
{
    public function run(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $database = $factory->createDatabase();

        // Data admin default
        $adminData = [
            'name' => 'Administrator',
            'email' => 'admin@ciats.com',
            'password' => Hash::make('admin123'), // Password: admin123
            'role' => 'admin',
            'created_at' => time(),
        ];

        try {
            // Cek apakah admin sudah ada
            $usersRef = $database->getReference('users')->getValue();
            $adminExists = false;
            
            if ($usersRef) {
                foreach ($usersRef as $user) {
                    if ($user['email'] === 'admin@ciats.com') {
                        $adminExists = true;
                        break;
                    }
                }
            }

            // Jika admin belum ada, buat baru
            if (!$adminExists) {
                $database->getReference('users')->push($adminData);
                $this->command->info('Admin user created successfully!');
                $this->command->info('Email: admin@ciats.com');
                $this->command->info('Password: admin123');
            } else {
                $this->command->info('Admin user already exists.');
            }
            
        } catch (\Exception $e) {
            $this->command->error('Error creating admin user: ' . $e->getMessage());
        }
    }
}