<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\Pages;

use App\Actions\Maintenance\CreateMaintenanceHistoryAction;
use App\Filament\Resources\EquipmentMaintenanceHistories\EquipmentMaintenanceHistoryResource;
use App\Models\EquipmentMaintenanceHistory;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEquipmentMaintenanceHistory extends CreateRecord
{
    protected static string $resource = EquipmentMaintenanceHistoryResource::class;

    protected function handleRecordCreation(array $data): EquipmentMaintenanceHistory
    {
        return app(CreateMaintenanceHistoryAction::class)->run($data, Auth::user());
    }
}
