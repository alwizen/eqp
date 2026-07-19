<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Actions\Vendor\CreateVendorAction;
use App\Filament\Resources\Vendors\VendorResource;
use App\Models\Vendor;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function handleRecordCreation(array $data): Vendor
    {
        return app(CreateVendorAction::class)->run($data);
    }
}
