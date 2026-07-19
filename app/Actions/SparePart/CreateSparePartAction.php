<?php

declare(strict_types=1);

namespace App\Actions\SparePart;

use App\Models\SparePart;
use Illuminate\Support\Facades\DB;

class CreateSparePartAction
{
    public function run(array $data): SparePart
    {
        return DB::transaction(function () use ($data) {
            return SparePart::query()->create([
                'part_number' => $data['part_number'] ?? null,
                'name' => $data['name'],
                'manufacturer' => $data['manufacturer'] ?? null,
                'specification' => $data['specification'] ?? null,
                'unit' => $data['unit'] ?? 'pcs',
                'current_stock' => (float) ($data['current_stock'] ?? 0),
                'minimum_stock' => (float) ($data['minimum_stock'] ?? 0),
                'unit_price' => (float) ($data['unit_price'] ?? 0),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
