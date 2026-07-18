<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceType: string
{
    case INSPECTION = 'inspection';
    case PREVENTIVE_MAINTENANCE = 'preventive_maintenance';
    case CORRECTIVE_MAINTENANCE = 'corrective_maintenance';
    case BREAKDOWN = 'breakdown';
    case OVERHAUL = 'overhaul';
    case REPLACEMENT = 'replacement';
    case CALIBRATION = 'calibration';
    case MODIFICATION = 'modification';
    case TESTING = 'testing';
    case CLEANING = 'cleaning';
    case LUBRICATION = 'lubrication';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::INSPECTION => 'Inspection',
            self::PREVENTIVE_MAINTENANCE => 'Preventive Maintenance',
            self::CORRECTIVE_MAINTENANCE => 'Corrective Maintenance',
            self::BREAKDOWN => 'Breakdown',
            self::OVERHAUL => 'Overhaul',
            self::REPLACEMENT => 'Replacement',
            self::CALIBRATION => 'Calibration',
            self::MODIFICATION => 'Modification',
            self::TESTING => 'Testing',
            self::CLEANING => 'Cleaning',
            self::LUBRICATION => 'Lubrication',
            self::OTHER => 'Other',
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
