<?php

declare(strict_types=1);

namespace App\Actions\Maintenance;

use App\Models\EquipmentMaintenanceHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateMaintenanceHistoryAction
{
    public function run(EquipmentMaintenanceHistory $history, array $data, User $user): EquipmentMaintenanceHistory
    {
        return DB::transaction(function () use ($history, $data, $user) {
            $materialCost = 0.0;

            if (array_key_exists('spare_parts', $data)) {
                $history->sparePartUsages()->delete();

                foreach ($data['spare_parts'] as $usageData) {
                    $quantity = (float) ($usageData['quantity'] ?? 0);
                    $unitPrice = (float) ($usageData['unit_price'] ?? 0);
                    $materialCost += $quantity * $unitPrice;

                    $history->sparePartUsages()->create([
                        'spare_part_id' => $usageData['spare_part_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $quantity * $unitPrice,
                        'notes' => $usageData['notes'] ?? null,
                    ]);
                }
            } else {
                $materialCost = (float) ($history->material_cost ?? 0);
            }

            $history->fill([
                'equipment_id' => $data['equipment_id'] ?? $history->equipment_id,
                'work_order_number' => $data['work_order_number'] ?? $history->work_order_number,
                'reported_at' => $data['reported_at'] ?? $history->reported_at,
                'scheduled_at' => $data['scheduled_at'] ?? $history->scheduled_at,
                'started_at' => $data['started_at'] ?? $history->started_at,
                'completed_at' => $data['completed_at'] ?? $history->completed_at,
                'maintenance_type' => $data['maintenance_type'] ?? $history->maintenance_type,
                'status' => $data['status'] ?? $history->status,
                'executor_type' => $data['executor_type'] ?? $history->executor_type,
                'vendor_id' => $data['vendor_id'] ?? $history->vendor_id,
                'internal_pic_user_id' => $data['internal_pic_user_id'] ?? $history->internal_pic_user_id,
                'technician_name' => $data['technician_name'] ?? $history->technician_name,
                'component' => $data['component'] ?? $history->component,
                'problem_description' => $data['problem_description'] ?? $history->problem_description,
                'root_cause' => $data['root_cause'] ?? $history->root_cause,
                'action_taken' => $data['action_taken'] ?? $history->action_taken,
                'recommendation' => $data['recommendation'] ?? $history->recommendation,
                'condition_before' => $data['condition_before'] ?? $history->condition_before,
                'condition_after' => $data['condition_after'] ?? $history->condition_after,
                'downtime_minutes' => $data['downtime_minutes'] ?? $history->downtime_minutes,
                'labor_cost' => $data['labor_cost'] ?? $history->labor_cost,
                'material_cost' => $materialCost,
                'other_cost' => $data['other_cost'] ?? $history->other_cost,
                'next_maintenance_at' => $data['next_maintenance_at'] ?? $history->next_maintenance_at,
                'notes' => $data['notes'] ?? $history->notes,
                'cancellation_reason' => $data['cancellation_reason'] ?? $history->cancellation_reason,
                'cancelled_at' => $data['cancelled_at'] ?? $history->cancelled_at,
                'updated_by' => $user->id,
            ]);

            $history->total_cost = $history->labor_cost + $history->material_cost + $history->other_cost;
            $history->save();

            return $history->fresh();
        });
    }
}
