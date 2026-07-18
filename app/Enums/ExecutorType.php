<?php

declare(strict_types=1);

namespace App\Enums;

enum ExecutorType: string
{
    case INTERNAL = 'internal';
    case VENDOR = 'vendor';
    case COMBINATION = 'combination';

    public function label(): string
    {
        return match ($this) {
            self::INTERNAL => 'Internal PIC',
            self::VENDOR => 'External Vendor',
            self::COMBINATION => 'Internal & Vendor',
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
