<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // 1. First specific example from Task sheet
        Equipment::create([
            'tag_no' => '1219-FSU-GV11/00',
            'technical_no' => '3"-GV-530',
            'description' => 'GV INLET DV-519',
            'functional_location' => '1219-FSU / FOAM SYSTEM',
            'status' => 'operational',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // 2. Second specific example from Task sheet
        Equipment::create([
            'tag_no' => '1219-PMP-P01/00',
            'technical_no' => 'P-001',
            'description' => 'MAIN TRANSFER PUMP',
            'functional_location' => 'PUMP HOUSE',
            'manufacturer' => 'Ebara',
            'model_type' => 'contoh model pompa',
            'status' => 'operational',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create some generic equipments
        Equipment::factory()->count(10)->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
