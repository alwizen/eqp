<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::create([
            'code' => 'VND-001',
            'name' => 'PT. Global Teknik Mandiri',
            'address' => 'Kawasan Industri Jababeka Blok C-12, Cikarang',
            'phone' => '021-8987654',
            'email' => 'info@globalteknik.com',
            'contact_person' => 'Budi Santoso',
            'scope_of_work' => 'Mechanical, Electrical, and Piping Services',
            'is_active' => true,
        ]);

        Vendor::create([
            'code' => 'VND-002',
            'name' => 'CV. Jaya Calibration',
            'address' => 'Jl. Raden Saleh No. 45, Jakarta Pusat',
            'phone' => '021-3908821',
            'email' => 'service@jayacalibration.co.id',
            'contact_person' => 'Andi Wijaya',
            'scope_of_work' => 'Instrument Calibration and Testing',
            'is_active' => true,
        ]);

        Vendor::factory()->count(5)->create();
    }
}
