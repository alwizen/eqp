<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EquipmentMaintenanceSparePart
 *
 * @property int $id
 * @property int $equipment_maintenance_history_id
 * @property int $spare_part_id
 * @property float $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property string|null $notes
 */
class EquipmentMaintenanceSparePart extends Model
{
    protected $table = 'equipment_maintenance_spare_parts';

    protected $fillable = [
        'equipment_maintenance_history_id',
        'spare_part_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'unit_price' => 'float',
            'total_price' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $usage) {
            $usage->total_price = $usage->quantity * $usage->unit_price;
        });
    }

    public function maintenanceHistory(): BelongsTo
    {
        return $this->belongsTo(EquipmentMaintenanceHistory::class, 'equipment_maintenance_history_id');
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
