<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EquipmentCondition;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class EquipmentMaintenanceHistory
 *
 * @property int $id
 * @property int $equipment_id
 * @property string $history_number
 * @property string|null $work_order_number
 * @property \Carbon\Carbon|null $reported_at
 * @property \Carbon\Carbon|null $scheduled_at
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property MaintenanceType $maintenance_type
 * @property MaintenanceStatus $status
 * @property ExecutorType $executor_type
 * @property int|null $vendor_id
 * @property int|null $internal_pic_user_id
 * @property string|null $technician_name
 * @property string|null $component
 * @property string|null $problem_description
 * @property string|null $root_cause
 * @property string|null $action_taken
 * @property string|null $recommendation
 * @property EquipmentCondition|null $condition_before
 * @property EquipmentCondition|null $condition_after
 * @property int $downtime_minutes
 * @property float $labor_cost
 * @property float $material_cost
 * @property float $other_cost
 * @property float $total_cost
 * @property \Carbon\Carbon|null $next_maintenance_at
 * @property string|null $notes
 * @property string|null $cancellation_reason
 * @property \Carbon\Carbon|null $cancelled_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class EquipmentMaintenanceHistory extends Model
{
    use HasFactory;

    protected $table = 'equipment_maintenance_histories';

    protected $fillable = [
        'equipment_id',
        'history_number',
        'work_order_number',
        'reported_at',
        'scheduled_at',
        'started_at',
        'completed_at',
        'maintenance_type',
        'status',
        'executor_type',
        'vendor_id',
        'internal_pic_user_id',
        'technician_name',
        'component',
        'problem_description',
        'root_cause',
        'action_taken',
        'recommendation',
        'condition_before',
        'condition_after',
        'downtime_minutes',
        'labor_cost',
        'material_cost',
        'other_cost',
        'total_cost',
        'next_maintenance_at',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'maintenance_type' => MaintenanceType::class,
            'status' => MaintenanceStatus::class,
            'executor_type' => ExecutorType::class,
            'condition_before' => EquipmentCondition::class,
            'condition_after' => EquipmentCondition::class,
            'downtime_minutes' => 'integer',
            'labor_cost' => 'float',
            'material_cost' => 'float',
            'other_cost' => 'float',
            'total_cost' => 'float',
            'next_maintenance_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $history) {
            // Ensure cost calculation logic is automated on save
            $history->total_cost = ($history->labor_cost ?? 0.0)
                + ($history->material_cost ?? 0.0)
                + ($history->other_cost ?? 0.0);
        });

        static::saved(function (self $history) {
            $equipment = $history->equipment()->first();

            if (! $equipment) {
                return;
            }

            if ($history->status === MaintenanceStatus::COMPLETED || ! empty($history->completed_at)) {
                $equipment->last_maintenance_at = $history->completed_at ?? $history->reported_at ?? now();

                if ($history->condition_after !== null) {
                    $equipment->latest_condition = $history->condition_after;
                }

                $equipment->saveQuietly();
            }
        });
    }

    /*
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function internalPic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'internal_pic_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceAttachment::class, 'equipment_maintenance_history_id');
    }

    public function sparePartUsages(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceSparePart::class, 'equipment_maintenance_history_id');
    }

    public function spareParts(): BelongsToMany
    {
        return $this->belongsToMany(
            SparePart::class,
            'equipment_maintenance_spare_parts',
            'equipment_maintenance_history_id',
            'spare_part_id'
        )->withPivot(['quantity', 'unit_price', 'total_price', 'notes'])
            ->withTimestamps();
    }

    /*
     * --------------------------------------------------------------------------
     * Scopes
     * --------------------------------------------------------------------------
     */

    public function scopeForEquipment(Builder $query, int $equipmentId): Builder
    {
        return $query->where('equipment_id', $equipmentId);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', MaintenanceStatus::COMPLETED->value);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            MaintenanceStatus::REPORTED->value,
            MaintenanceStatus::SCHEDULED->value,
            MaintenanceStatus::IN_PROGRESS->value,
        ]);
    }

    public function scopeByMaintenanceType(Builder $query, MaintenanceType $type): Builder
    {
        return $query->where('maintenance_type', $type->value);
    }

    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('history_number', 'like', "%{$search}%")
                ->orWhere('work_order_number', 'like', "%{$search}%")
                ->orWhere('component', 'like', "%{$search}%")
                ->orWhere('problem_description', 'like', "%{$search}%")
                ->orWhere('root_cause', 'like', "%{$search}%")
                ->orWhere('action_taken', 'like', "%{$search}%")
                ->orWhere('technician_name', 'like', "%{$search}%");
        });
    }
}
