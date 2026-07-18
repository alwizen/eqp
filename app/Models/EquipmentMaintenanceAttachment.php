<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttachmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EquipmentMaintenanceAttachment
 *
 * @property int $id
 * @property int $equipment_maintenance_history_id
 * @property AttachmentCategory $category
 * @property string $original_name
 * @property string $file_name
 * @property string $file_path
 * @property string $disk
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property string|null $description
 * @property int|null $uploaded_by
 */
class EquipmentMaintenanceAttachment extends Model
{
    protected $table = 'equipment_maintenance_attachments';

    protected $fillable = [
        'equipment_maintenance_history_id',
        'category',
        'original_name',
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'description',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'category' => AttachmentCategory::class,
            'file_size' => 'integer',
        ];
    }

    public function maintenanceHistory(): BelongsTo
    {
        return $this->belongsTo(EquipmentMaintenanceHistory::class, 'equipment_maintenance_history_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
