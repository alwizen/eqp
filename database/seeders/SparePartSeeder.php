<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SparePart::create([
            'part_number' => 'GKT-1002-N',
            'name' => 'Neoprene Flange Gasket 3"',
            'manufacturer' => 'Klinger',
            'specification' => 'ANSI 150#, thickness 3mm',
            'unit' => 'pcs',
            'current_stock' => 50,
            'minimum_stock' => 10,
            'unit_price' => 45000,
            'is_active' => true,
        ]);

        SparePart::create([
            'part_number' => 'SEAL-PMP-01',
            'name' => 'Mechanical Seal Type 21',
            'manufacturer' => 'John Crane',
            'specification' => 'Shaft size 1.5 inch, carbon/ceramic/NBR',
            'unit' => 'set',
            'current_stock' => 8,
            'minimum_stock' => 2,
            'unit_price' => 850000,
            'is_active' => true,
        ]);

        SparePart::factory()->count(10)->create();
    }
}
