<?php

declare(strict_types=1);

namespace App\Enums;

enum EquipmentCondition: string
{
    case GOOD = 'good';
    case MINOR_ISSUE = 'minor_issue';
    case MAJOR_ISSUE = 'major_issue';
    case DAMAGED = 'damaged';
    case NOT_OPERATIONAL = 'not_operational';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Good',
            self::MINOR_ISSUE => 'Minor Issue',
            self::MAJOR_ISSUE => 'Major Issue',
            self::DAMAGED => 'Damaged',
            self::NOT_OPERATIONAL => 'Not Operational',
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
