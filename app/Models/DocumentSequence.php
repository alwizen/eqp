<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentSequence
 *
 * @property int $id
 * @property string $document_type
 * @property int $year
 * @property int|null $month
 * @property int $last_number
 */
class DocumentSequence extends Model
{
    protected $fillable = [
        'document_type',
        'year',
        'month',
        'last_number',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'last_number' => 'integer',
        ];
    }
}
