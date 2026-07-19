<?php

declare(strict_types=1);

namespace App\Actions\Vendor;

use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class CreateVendorAction
{
    public function run(array $data): Vendor
    {
        return DB::transaction(function () use ($data) {
            return Vendor::query()->create([
                'code' => $data['code'] ?? null,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'scope_of_work' => $data['scope_of_work'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
