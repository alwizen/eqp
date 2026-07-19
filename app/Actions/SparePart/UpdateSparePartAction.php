<?php

declare(strict_types=1);

namespace App\Actions\SparePart;

use App\Models\SparePart;
use Illuminate\Support\Facades\DB;

class UpdateSparePartAction
{
    public function run(SparePart $sparePart, array $data): SparePart
    {
        return DB::transaction(function () use ($sparePart, $data) {
            $sparePart->fill([
                'part_number' => $data['part_number'] ?? $sparePart->part_number,
                'name' => $data['name'] ?? $sparePart->name,
                'manufacturer' => $data['manufacturer'] ?? $sparePart->manufacturer,
                'specification' => $data['specification'] ?? $sparePart->specification,
                'unit' => $data['unit'] ?? $sparePart->unit,
                'current_stock' => isset($data['current_stock']) ? (float) $data['current_stock'] : $sparePart->current_stock,
                'minimum_stock' => isset($data['minimum_stock']) ? (float) $data['minimum_stock'] : $sparePart->minimum_stock,
                'unit_price' => isset($data['unit_price']) ? (float) $data['unit_price'] : $sparePart->unit_price,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $sparePart->is_active,
                'notes' => $data['notes'] ?? $sparePart->notes,
            ]);

            $sparePart->save();

            return $sparePart->fresh();
        });
    }
}
