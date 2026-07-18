<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EquipmentCondition;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceHistory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentMaintenanceHistoryFactory extends Factory
{
    protected $model = EquipmentMaintenanceHistory::class;

    public function definition(): array
    {
        return [
            'equipment_id' => Equipment::factory(),
            'history_number' => 'MTN/' . $this->faker->year() . '/' . str_pad((string)$this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT) . '/' . str_pad((string)$this->faker->unique()->numberBetween(1, 99999), 6, '0', STR_PAD_LEFT),
            'work_order_number' => 'WO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'reported_at' => now()->subDays(10),
            'scheduled_at' => now()->subDays(8),
            'started_at' => now()->subDays(7),
            'completed_at' => null,
            'maintenance_type' => MaintenanceType::PREVENTIVE_MAINTENANCE->value,
            'status' => MaintenanceStatus::DRAFT->value,
            'executor_type' => ExecutorType::INTERNAL->value,
            'vendor_id' => null,
            'internal_pic_user_id' => User::factory(),
            'technician_name' => $this->faker->name(),
            'component' => $this->faker->word(),
            'problem_description' => $this->faker->sentence(10),
            'root_cause' => $this->faker->sentence(10),
            'action_taken' => null,
            'recommendation' => $this->faker->sentence(5),
            'condition_before' => EquipmentCondition::MINOR_ISSUE->value,
            'condition_after' => null,
            'downtime_minutes' => 0,
            'labor_cost' => 0.0,
            'material_cost' => 0.0,
            'other_cost' => 0.0,
            'total_cost' => 0.0,
            'next_maintenance_at' => null,
            'notes' => $this->faker->paragraph(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function draft(): self
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::DRAFT->value,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): self
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::IN_PROGRESS->value,
            'started_at' => now()->subHours(2),
            'completed_at' => null,
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::COMPLETED->value,
            'completed_at' => now()->subHour(),
            'condition_after' => EquipmentCondition::GOOD->value,
            'action_taken' => 'Cleaned filter and tightened bolts.',
            'next_maintenance_at' => now()->addMonths(3),
        ]);
    }

    public function vendorExecuted(): self
    {
        return $this->state(fn () => [
            'executor_type' => ExecutorType::VENDOR->value,
            'vendor_id' => Vendor::factory(),
            'internal_pic_user_id' => null,
        ]);
    }

    public function internallyExecuted(): self
    {
        return $this->state(fn () => [
            'executor_type' => ExecutorType::INTERNAL->value,
            'vendor_id' => null,
            'internal_pic_user_id' => User::factory(),
        ]);
    }
}
