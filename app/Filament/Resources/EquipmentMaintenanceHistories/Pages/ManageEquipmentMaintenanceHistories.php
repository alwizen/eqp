<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\Pages;

use App\Filament\Resources\EquipmentMaintenanceHistories\EquipmentMaintenanceHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEquipmentMaintenanceHistories extends ManageRecords
{
    protected static string $resource = EquipmentMaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
