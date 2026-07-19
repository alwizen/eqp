<?php

declare(strict_types=1);

namespace App\Actions\Maintenance;

use App\Models\EquipmentMaintenanceHistory;
use Illuminate\Support\Facades\DB;

class DeleteMaintenanceHistoryAction
{
    public function run(EquipmentMaintenanceHistory $history): bool
    {
        return DB::transaction(function () use ($history) {
            $history->attachments()->delete();
            $history->sparePartUsages()->delete();

            return $history->delete(false);
        });
    }
}
