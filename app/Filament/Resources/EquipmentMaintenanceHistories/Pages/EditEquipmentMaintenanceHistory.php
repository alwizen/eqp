<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\Pages;

use App\Actions\Maintenance\UpdateMaintenanceHistoryAction;
use App\Filament\Resources\EquipmentMaintenanceHistories\EquipmentMaintenanceHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditEquipmentMaintenanceHistory extends EditRecord
{
    protected static string $resource = EquipmentMaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpdateMaintenanceHistoryAction::class)->run($record, $data, Auth::user());
    }
}
