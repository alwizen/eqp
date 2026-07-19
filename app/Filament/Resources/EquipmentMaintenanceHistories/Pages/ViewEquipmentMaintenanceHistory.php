<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\Pages;

use App\Filament\Resources\EquipmentMaintenanceHistories\EquipmentMaintenanceHistoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEquipmentMaintenanceHistory extends ViewRecord
{
    protected static string $resource = EquipmentMaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
