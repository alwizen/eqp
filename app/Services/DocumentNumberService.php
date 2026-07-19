<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentSequence;
use Carbon\Carbon;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public function generate(string $documentType, ?int $year = null, ?int $month = null, string $prefix = 'MTN'): string
    {
        $year ??= (int) now()->year;
        $month ??= (int) now()->month;

        return DB::transaction(function () use ($documentType, $year, $month, $prefix) {
            $sequence = DocumentSequence::query()
                ->where('document_type', $documentType)
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                $sequence = DocumentSequence::query()->create([
                    'document_type' => $documentType,
                    'year' => $year,
                    'month' => $month,
                    'last_number' => 0,
                ]);
            }

            $sequence->last_number += 1;
            $sequence->save();

            return sprintf(
                '%s/%04d/%02d/%06d',
                strtoupper($prefix),
                $year,
                $month,
                $sequence->last_number
            );
        });
    }
}
