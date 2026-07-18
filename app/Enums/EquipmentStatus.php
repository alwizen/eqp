<?php

declare(strict_types=1);

namespace App\Enums;

enum EquipmentStatus: string
{
    case OPERATIONAL = 'operational';
    case STANDBY = 'standby';
    case UNDER_INSPECTION = 'under_inspection';
    case UNDER_MAINTENANCE = 'under_maintenance';
    case BREAKDOWN = 'breakdown';
    case OUT_OF_SERVICE = 'out_of_service';
    case DECOMMISSIONED = 'decommissioned';

    public function label(): string
    {
        return match ($this) {
            self::OPERATIONAL => 'Operational',
            self::STANDBY => 'Standby',
            self::UNDER_INSPECTION => 'Under Inspection',
            self::UNDER_MAINTENANCE => 'Under Maintenance',
            self::BREAKDOWN => 'Breakdown',
            self::OUT_OF_SERVICE => 'Out of Service',
            self::DECOMMISSIONED => 'Decommissioned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPERATIONAL => 'success',
            self::STANDBY => 'warning',
            self::UNDER_INSPECTION => 'info',
            self::UNDER_MAINTENANCE => 'danger',
            self::BREAKDOWN => 'danger',
            self::OUT_OF_SERVICE => 'gray',
            self::DECOMMISSIONED => 'gray',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
