<?php

declare(strict_types=1);

namespace App\Actions\Equipment;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateEquipmentAction
{
    public function run(Equipment $equipment, array $data, User $user): Equipment
    {
        return DB::transaction(function () use ($equipment, $data, $user) {
            $equipment->fill([
                'tag_no' => $data['tag_no'] ?? $equipment->tag_no,
                'technical_no' => $data['technical_no'] ?? $equipment->technical_no,
                'description' => $data['description'] ?? $equipment->description,
                'functional_location' => $data['functional_location'] ?? $equipment->functional_location,
                'manufacturer' => $data['manufacturer'] ?? $equipment->manufacturer,
                'model_type' => $data['model_type'] ?? $equipment->model_type,
                'serial_number' => $data['serial_number'] ?? $equipment->serial_number,
                'category' => $data['category'] ?? $equipment->category,
                'installation_date' => $data['installation_date'] ?? $equipment->installation_date,
                'status' => $data['status'] ?? $equipment->status,
                'latest_condition' => $data['latest_condition'] ?? $equipment->latest_condition,
                'last_maintenance_at' => $data['last_maintenance_at'] ?? $equipment->last_maintenance_at,
                'next_maintenance_at' => $data['next_maintenance_at'] ?? $equipment->next_maintenance_at,
                'notes' => $data['notes'] ?? $equipment->notes,
                'updated_by' => $user->id,
            ]);

            $equipment->save();

            return $equipment->fresh();
        });
    }
}
