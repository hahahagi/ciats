<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kreait\Firebase\Factory;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FirebaseBulkSeeder extends Seeder
{
    public function run(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $database = $factory->createDatabase();
        $faker = Faker::create('id_ID');

        $this->command->info('Mulai proses seeding data ke Firebase...');

        // 1. Seed Categories
        $categories = ['Laptop', 'Monitor', 'Keyboard', 'Mouse', 'Printer', 'Projector', 'Tablet', 'Smartphone', 'Server', 'Switch'];
        $categorySlugs = [];

        $this->command->info('Mengisi data Kategori...');
        // Hapus kategori lama jika perlu (opsional, di sini kita append saja)
        // $database->getReference('categories')->remove();

        foreach ($categories as $catName) {
            $slug = strtolower($catName);

            // Push ke Firebase
            $database->getReference('categories')->push([
                'name' => $catName,
                'slug' => $slug,
                'description' => 'Kategori untuk perangkat ' . $catName,
                'created_at' => time()
            ]);
            $categorySlugs[] = $slug;
        }

        // 2. Seed Locations
        $locations = [
            'Ruang Server Utama',
            'Gudang Logistik',
            'Lobby Depan',
            'Ruang Meeting Alpha',
            'Ruang Meeting Beta',
            'Area Kerja Staff IT',
            'Area Kerja HRD',
            'Area Kerja Finance',
            'Pantry Lt 1',
            'Musholla'
        ];

        $this->command->info('Mengisi data Lokasi...');
        // $database->getReference('locations')->remove();

        foreach ($locations as $locName) {
            $database->getReference('locations')->push([
                'name' => $locName,
                'floor' => rand(1, 3),
                'description' => 'Lokasi operasional di ' . $locName,
                'created_at' => time()
            ]);
        }

        // 3. Seed Assets
        $numberOfAssets = 50; // Jumlah aset yang ingin dibuat
        $this->command->info("Mengisi data $numberOfAssets Aset...");
        // $database->getReference('assets')->remove();

        for ($i = 0; $i < $numberOfAssets; $i++) {
            $cat = $faker->randomElement($categorySlugs);
            $loc = $faker->randomElement($locations);

            // Nama aset yang lebih realistis
            $brands = ['Dell', 'HP', 'Lenovo', 'Apple', 'Samsung', 'Logitech', 'Epson', 'Canon'];
            $brand = $faker->randomElement($brands);
            $name = "$brand $cat " . $faker->bothify('##??');

            $status = $faker->randomElement(['available', 'available', 'available', 'in_use', 'maintenance', 'damaged']);
            $serialNumber = strtoupper($faker->bothify('SN-????-#####'));

            // Generate QR Code
            $qrUrl = '';
            try {
                $qrContent = json_encode([
                    'serial' => $serialNumber,
                    'name' => $name,
                    'type' => 'asset',
                    'time' => time()
                ]);

                $directory = storage_path('app/public/qrcodes');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $filename = 'qrcode_' . $serialNumber . '_' . time() . '.svg';
                $path = $directory . '/' . $filename;

                QrCode::format('svg')->size(300)->generate($qrContent, $path);
                $qrUrl = '/storage/qrcodes/' . $filename;
            } catch (\Exception $e) {
                // Ignore if library not ready
            }

            $assetData = [
                'name' => $name,
                'category' => $cat,
                'serial_number' => $serialNumber,
                'location' => $loc,
                'description' => $faker->sentence,
                'status' => $status,
                'qr_code_url' => $qrUrl,
                'booked' => false,
                'current_holder' => ($status == 'in_use') ? $faker->name : null,
                'purchase_date' => $faker->date('Y-m-d', 'now'),
                'price' => $faker->numberBetween(1000000, 25000000),
                'created_at' => time(),
                'updated_at' => time(),
            ];

            $database->getReference('assets')->push($assetData);

            // Progress bar sederhana
            if (($i + 1) % 10 == 0) {
                $this->command->info("Berhasil membuat " . ($i + 1) . " aset...");
            }
        }

        $this->command->info('Seeding selesai! Data berhasil ditambahkan ke Firebase.');
    }
}
