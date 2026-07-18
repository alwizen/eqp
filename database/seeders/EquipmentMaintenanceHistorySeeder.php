<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EquipmentCondition;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceHistory;
use App\Models\SparePart;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class EquipmentMaintenanceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $equipments = Equipment::all();
        $vendors = Vendor::all();
        $spareParts = SparePart::all();

        if ($equipments->isEmpty() || $vendors->isEmpty() || $spareParts->isEmpty()) {
            return;
        }

        // 1. Completed Internal Preventive Maintenance
        $eq1 = $equipments->first();
        $history1 = EquipmentMaintenanceHistory::create([
            'equipment_id' => $eq1->id,
            'history_number' => 'MTN/2026/07/000001',
            'work_order_number' => 'WO-2026-0001',
            'reported_at' => now()->subDays(5),
            'scheduled_at' => now()->subDays(4),
            'started_at' => now()->subDays(4)->addHours(2),
            'completed_at' => now()->subDays(4)->addHours(6),
            'maintenance_type' => MaintenanceType::PREVENTIVE_MAINTENANCE->value,
            'status' => MaintenanceStatus::COMPLETED->value,
            'executor_type' => ExecutorType::INTERNAL->value,
            'vendor_id' => null,
            'internal_pic_user_id' => $user->id,
            'technician_name' => 'Fadli Rahman',
            'component' => 'Valve Actuator',
            'problem_description' => 'Jadwal rutin bulanan pengecekan valve.',
            'root_cause' => 'Pemeliharaan berkala.',
            'action_taken' => 'Pembersihan karat, pelumasan shaft actuator, pengetesan pressure.',
            'recommendation' => 'Ganti Actuator O-ring pada bulan depan.',
            'condition_before' => EquipmentCondition::GOOD->value,
            'condition_after' => EquipmentCondition::GOOD->value,
            'downtime_minutes' => 240,
            'labor_cost' => 150000,
            'material_cost' => 45000, // gasket used
            'other_cost' => 0,
            'total_cost' => 195000,
            'next_maintenance_at' => now()->addMonths(1),
            'notes' => 'Berjalan lancar.',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Attach gasket spare part usage
        $gasket = $spareParts->where('part_number', 'GKT-1002-N')->first();
        if ($gasket) {
            $history1->sparePartUsages()->create([
                'spare_part_id' => $gasket->id,
                'quantity' => 1.000,
                'unit_price' => $gasket->unit_price,
                'total_price' => $gasket->unit_price * 1,
            ]);
            $gasket->decrement('current_stock', 1);
        }

        // 2. In Progress Vendor Corrective Maintenance
        $eq2 = $equipments->skip(1)->first() ?? $eq1;
        $vendor1 = $vendors->first();
        $history2 = EquipmentMaintenanceHistory::create([
            'equipment_id' => $eq2->id,
            'history_number' => 'MTN/2026/07/000002',
            'work_order_number' => 'WO-2026-0002',
            'reported_at' => now()->subDays(1),
            'scheduled_at' => now(),
            'started_at' => now()->subHours(4),
            'completed_at' => null,
            'maintenance_type' => MaintenanceType::CORRECTIVE_MAINTENANCE->value,
            'status' => MaintenanceStatus::IN_PROGRESS->value,
            'executor_type' => ExecutorType::VENDOR->value,
            'vendor_id' => $vendor1->id,
            'internal_pic_user_id' => null,
            'technician_name' => 'Adit - Vendor Team',
            'component' => 'Impeller Pump',
            'problem_description' => 'Pompa transfer berisik dan vibrasi tinggi.',
            'root_cause' => 'Bantalan / bearing aus.',
            'action_taken' => 'Membongkar pump head, menyiapkan bearing cadangan.',
            'recommendation' => null,
            'condition_before' => EquipmentCondition::MAJOR_ISSUE->value,
            'condition_after' => null,
            'downtime_minutes' => 0,
            'labor_cost' => 1200000,
            'material_cost' => 850000, // mechanical seal
            'other_cost' => 100000,
            'total_cost' => 2150000,
            'next_maintenance_at' => null,
            'notes' => 'Pekerjaan sedang berlangsung oleh vendor.',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $seal = $spareParts->where('part_number', 'SEAL-PMP-01')->first();
        if ($seal) {
            $history2->sparePartUsages()->create([
                'spare_part_id' => $seal->id,
                'quantity' => 1.000,
                'unit_price' => $seal->unit_price,
                'total_price' => $seal->unit_price * 1,
            ]);
            $seal->decrement('current_stock', 1);
        }
    }
}
