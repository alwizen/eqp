<?php

namespace App\Filament\Resources\SpareParts\Pages;

use App\Actions\SparePart\CreateSparePartAction;
use App\Filament\Resources\SpareParts\SparePartResource;
use App\Models\SparePart;
use Filament\Resources\Pages\CreateRecord;

class CreateSparePart extends CreateRecord
{
    protected static string $resource = SparePartResource::class;

    protected function handleRecordCreation(array $data): SparePart
    {
        return app(CreateSparePartAction::class)->run($data);
    }
}
