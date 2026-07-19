<?php

declare(strict_types=1);

namespace App\Actions\Vendor;

use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class UpdateVendorAction
{
    public function run(Vendor $vendor, array $data): Vendor
    {
        return DB::transaction(function () use ($vendor, $data) {
            $vendor->fill([
                'code' => $data['code'] ?? $vendor->code,
                'name' => $data['name'] ?? $vendor->name,
                'address' => $data['address'] ?? $vendor->address,
                'phone' => $data['phone'] ?? $vendor->phone,
                'email' => $data['email'] ?? $vendor->email,
                'contact_person' => $data['contact_person'] ?? $vendor->contact_person,
                'scope_of_work' => $data['scope_of_work'] ?? $vendor->scope_of_work,
                'is_active' => $data['is_active'] ?? $vendor->is_active,
                'notes' => $data['notes'] ?? $vendor->notes,
            ]);

            $vendor->save();

            return $vendor->fresh();
        });
    }
}
