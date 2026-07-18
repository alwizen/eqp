<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SparePart
 *
 * @property int $id
 * @property string|null $part_number
 * @property string $name
 * @property string|null $manufacturer
 * @property string|null $specification
 * @property string $unit
 * @property float $current_stock
 * @property float $minimum_stock
 * @property float $unit_price
 * @property bool $is_active
 * @property string|null $notes
 */
class SparePart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_number',
        'name',
        'manufacturer',
        'specification',
        'unit',
        'current_stock',
        'minimum_stock',
        'unit_price',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'float',
            'minimum_stock' => 'float',
            'unit_price' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('part_number', 'like', "%{$search}%")
            ->orWhere('manufacturer', 'like', "%{$search}%");
    }

    public function maintenanceUsages(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceSparePart::class, 'spare_part_id');
    }

    public function maintenanceHistories(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentMaintenanceHistory::class,
            'equipment_maintenance_spare_parts',
            'spare_part_id',
            'equipment_maintenance_history_id'
        )->withPivot(['quantity', 'unit_price', 'total_price', 'notes'])
         ->withTimestamps();
    }
}
