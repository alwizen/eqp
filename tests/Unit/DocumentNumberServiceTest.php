<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\DocumentSequence;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_sequential_document_numbers_per_month(): void
    {
        $service = app(DocumentNumberService::class);

        $first = $service->generate('maintenance_history', 2026, 7);
        $second = $service->generate('maintenance_history', 2026, 7);

        $this->assertSame('MTN/2026/07/000001', $first);
        $this->assertSame('MTN/2026/07/000002', $second);

        $this->assertDatabaseHas('document_sequences', [
            'document_type' => 'maintenance_history',
            'year' => 2026,
            'month' => 7,
            'last_number' => 2,
        ]);
    }
}
