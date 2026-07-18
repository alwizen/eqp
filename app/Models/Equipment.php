<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EquipmentCondition;
use App\Enums\EquipmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Equipment
 *
 * @property int $id
 * @property string $tag_no
 * @property string|null $technical_no
 * @property string $description
 * @property string|null $functional_location
 * @property string|null $manufacturer
 * @property string|null $model_type
 * @property string|null $serial_number
 * @property string|null $category
 * @property \Carbon\Carbon|null $installation_date
 * @property EquipmentStatus $status
 * @property EquipmentCondition|null $latest_condition
 * @property \Carbon\Carbon|null $last_maintenance_at
 * @property \Carbon\Carbon|null $next_maintenance_at
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'tag_no',
        'technical_no',
        'description',
        'functional_location',
        'manufacturer',
        'model_type',
        'serial_number',
        'category',
        'installation_date',
        'status',
        'latest_condition',
        'last_maintenance_at',
        'next_maintenance_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'status' => EquipmentStatus::class,
            'latest_condition' => EquipmentCondition::class,
            'last_maintenance_at' => 'datetime',
            'next_maintenance_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $equipment) {
            $equipment->tag_no = trim($equipment->tag_no);
            if ($equipment->technical_no !== null) {
                $equipment->technical_no = trim($equipment->technical_no);
            }

            // Convert empty string properties to null
            foreach ($equipment->getAttributes() as $key => $value) {
                if ($value === '') {
                    $equipment->attributes[$key] = null;
                }
            }
        });
    }

    /*
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function maintenanceHistories(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceHistory::class, 'equipment_id');
    }

    public function attachments(): HasManyThrough
    {
        return $this->hasManyThrough(
            EquipmentMaintenanceAttachment::class,
            EquipmentMaintenanceHistory::class,
            'equipment_id', // Foreign key on histories table
            'equipment_maintenance_history_id', // Foreign key on attachments table
            'id', // Local key on equipments table
            'id' // Local key on histories table
        );
    }

    public function latestMaintenanceHistory(): HasOne
    {
        return $this->hasOne(EquipmentMaintenanceHistory::class, 'equipment_id')
            ->latestOfMany('completed_at');
    }

    public function completedMaintenanceHistories(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceHistory::class, 'equipment_id')
            ->where('status', \App\Enums\MaintenanceStatus::COMPLETED->value);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
     * --------------------------------------------------------------------------
     * Scopes
     * --------------------------------------------------------------------------
     */

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('tag_no', 'like', "%{$search}%")
                ->orWhere('technical_no', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('functional_location', 'like', "%{$search}%")
                ->orWhere('manufacturer', 'like', "%{$search}%")
                ->orWhere('model_type', 'like', "%{$search}%");
        });
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query->where('status', EquipmentStatus::OPERATIONAL->value);
    }

    public function scopeDueForMaintenance(Builder $query): Builder
    {
        return $query->whereNotNull('next_maintenance_at')
            ->where('next_maintenance_at', '<=', now()->addDays(7));
    }

    public function scopeOverdueMaintenance(Builder $query): Builder
    {
        return $query->whereNotNull('next_maintenance_at')
            ->where('next_maintenance_at', '<', now());
    }

    public function scopeByFunctionalLocation(Builder $query, string $location): Builder
    {
        return $query->where('functional_location', 'like', "%{$location}%");
    }

    public function scopeByStatus(Builder $query, EquipmentStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }
}
