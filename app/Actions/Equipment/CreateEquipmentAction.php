<?php

declare(strict_types=1);

namespace App\Actions\Equipment;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateEquipmentAction
{
    public function run(array $data, User $user): Equipment
    {
        return DB::transaction(function () use ($data, $user) {
            return Equipment::query()->create([
                'tag_no' => $data['tag_no'],
                'technical_no' => $data['technical_no'] ?? null,
                'description' => $data['description'],
                'functional_location' => $data['functional_location'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'model_type' => $data['model_type'] ?? null,
                'serial_number' => $data['serial_number'] ?? null,
                'category' => $data['category'] ?? null,
                'installation_date' => $data['installation_date'] ?? null,
                'status' => $data['status'],
                'latest_condition' => $data['latest_condition'] ?? null,
                'last_maintenance_at' => $data['last_maintenance_at'] ?? null,
                'next_maintenance_at' => $data['next_maintenance_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        });
    }
}
