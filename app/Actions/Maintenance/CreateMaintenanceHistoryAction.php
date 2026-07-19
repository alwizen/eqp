<?php

declare(strict_types=1);

namespace App\Actions\Maintenance;

use App\Enums\MaintenanceStatus;
use App\Models\EquipmentMaintenanceHistory;
use App\Models\EquipmentMaintenanceSparePart;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;

class CreateMaintenanceHistoryAction
{
    public function __construct(protected DocumentNumberService $documentNumberService)
    {
    }

    public function run(array $data, User $user): EquipmentMaintenanceHistory
    {
        return DB::transaction(function () use ($data, $user) {
            $laborCost = (float) ($data['labor_cost'] ?? 0);
            $otherCost = (float) ($data['other_cost'] ?? 0);
            $materialCost = $this->calculateMaterialCost($data['spare_parts'] ?? []);

            $history = EquipmentMaintenanceHistory::query()->create([
                'equipment_id' => $data['equipment_id'],
                'history_number' => $this->documentNumberService->generate('maintenance_history'),
                'work_order_number' => $data['work_order_number'] ?? null,
                'reported_at' => $data['reported_at'] ?? now(),
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'completed_at' => $data['completed_at'] ?? null,
                'maintenance_type' => $data['maintenance_type'],
                'status' => $data['status'] ?? MaintenanceStatus::DRAFT->value,
                'executor_type' => $data['executor_type'],
                'vendor_id' => $data['vendor_id'] ?? null,
                'internal_pic_user_id' => $data['internal_pic_user_id'] ?? null,
                'technician_name' => $data['technician_name'] ?? null,
                'component' => $data['component'] ?? null,
                'problem_description' => $data['problem_description'] ?? null,
                'root_cause' => $data['root_cause'] ?? null,
                'action_taken' => $data['action_taken'] ?? null,
                'recommendation' => $data['recommendation'] ?? null,
                'condition_before' => $data['condition_before'] ?? null,
                'condition_after' => $data['condition_after'] ?? null,
                'downtime_minutes' => (int) ($data['downtime_minutes'] ?? 0),
                'labor_cost' => $laborCost,
                'material_cost' => $materialCost,
                'other_cost' => $otherCost,
                'total_cost' => 0,
                'next_maintenance_at' => $data['next_maintenance_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'cancelled_at' => $data['cancelled_at'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            foreach ($data['spare_parts'] ?? [] as $usageData) {
                $history->sparePartUsages()->create([
                    'spare_part_id' => $usageData['spare_part_id'],
                    'quantity' => (float) ($usageData['quantity'] ?? 0),
                    'unit_price' => (float) ($usageData['unit_price'] ?? 0),
                    'total_price' => (float) ($usageData['quantity'] ?? 0) * (float) ($usageData['unit_price'] ?? 0),
                    'notes' => $usageData['notes'] ?? null,
                ]);
            }

            $history->refresh();
            $history->total_cost = $history->labor_cost + $history->material_cost + $history->other_cost;
            $history->save();

            return $history->fresh();
        });
    }

    protected function calculateMaterialCost(array $spareParts): float
    {
        $total = 0.0;

        foreach ($spareParts as $usageData) {
            $quantity = (float) ($usageData['quantity'] ?? 0);
            $unitPrice = (float) ($usageData['unit_price'] ?? 0);
            $total += $quantity * $unitPrice;
        }

        return round($total, 2);
    }
}
