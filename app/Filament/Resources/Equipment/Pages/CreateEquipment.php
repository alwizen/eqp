<?php

namespace App\Filament\Resources\Equipment\Pages;

use App\Actions\Equipment\CreateEquipmentAction;
use App\Filament\Resources\Equipment\EquipmentResource;
use App\Models\Equipment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function handleRecordCreation(array $data): Equipment
    {
        return app(CreateEquipmentAction::class)->run($data, Auth::user());
    }
}
