<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Maintenance\CreateMaintenanceHistoryAction;
use App\Enums\EquipmentCondition;
use App\Enums\EquipmentStatus;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Equipment;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentMaintenanceHistoryActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_maintenance_history_action_calculates_material_and_total_costs(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sparePart = SparePart::factory()->create([
            'unit_price' => 1000,
            'current_stock' => 10,
            'minimum_stock' => 2,
            'is_active' => true,
        ]);

        $action = app(CreateMaintenanceHistoryAction::class);
        $history = $action->run([
            'equipment_id' => $equipment->id,
            'maintenance_type' => MaintenanceType::CORRECTIVE_MAINTENANCE,
            'status' => MaintenanceStatus::REPORTED,
            'executor_type' => ExecutorType::INTERNAL,
            'labor_cost' => 2500,
            'other_cost' => 300,
            'spare_parts' => [
                [
                    'spare_part_id' => $sparePart->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                ],
            ],
        ], $user);

        $this->assertSame(2000.0, $history->material_cost);
        $this->assertSame(4800.0, $history->total_cost);
        $this->assertCount(1, $history->sparePartUsages()->get());
    }
}
