<?php

declare(strict_types=1);

namespace App\Enums;

enum AttachmentCategory: string
{
    case BEFORE_PHOTO = 'before_photo';
    case AFTER_PHOTO = 'after_photo';
    case INSPECTION_REPORT = 'inspection_report';
    case WORK_REPORT = 'work_report';
    case CERTIFICATE = 'certificate';
    case INVOICE = 'invoice';
    case DRAWING = 'drawing';
    case DATASHEET = 'datasheet';
    case CHECKLIST = 'checklist';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BEFORE_PHOTO => 'Before Photo',
            self::AFTER_PHOTO => 'After Photo',
            self::INSPECTION_REPORT => 'Inspection Report',
            self::WORK_REPORT => 'Work Report',
            self::CERTIFICATE => 'Certificate',
            self::INVOICE => 'Invoice',
            self::DRAWING => 'Drawing',
            self::DATASHEET => 'Datasheet',
            self::CHECKLIST => 'Checklist',
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
