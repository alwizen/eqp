<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Maintenance\CreateMaintenanceHistoryAction;
use App\Enums\EquipmentCondition;
use App\Enums\EquipmentStatus;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceHistory;
use App\Models\SparePart;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EquipmentMaintenanceHistoryActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_maintenance_history_action_calculates_material_and_total_costs(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sparePart = SparePart::factory()->create([
            'unit_price' => 1000,
            'current_stock' => 10,
            'minimum_stock' => 2,
            'is_active' => true,
        ]);

        $uploadedFile = UploadedFile::fake()->create('attachment.pdf', 20);

        $action = app(CreateMaintenanceHistoryAction::class);
        $history = $action->run([
            'equipment_id' => $equipment->id,
            'maintenance_type' => MaintenanceType::CORRECTIVE_MAINTENANCE,
            'status' => MaintenanceStatus::REPORTED,
            'executor_type' => ExecutorType::INTERNAL,
            'labor_cost' => 2500,
            'other_cost' => 300,
            'spare_parts' => [
                [
                    'spare_part_id' => $sparePart->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                ],
            ],
            'attachments' => [
                $uploadedFile,
            ],
        ], $user);

        $this->assertSame(2000.0, $history->material_cost);
        $this->assertSame(4800.0, $history->total_cost);
        $this->assertCount(1, $history->sparePartUsages()->get());
        $this->assertCount(1, $history->attachments()->get());
        $this->assertTrue(Storage::disk('public')->exists($history->attachments()->first()->file_path));
    }

    public function test_create_maintenance_history_action_generates_history_and_work_order_numbers_automatically(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $action = app(CreateMaintenanceHistoryAction::class);
        $history = $action->run([
            'equipment_id' => $equipment->id,
            'maintenance_type' => MaintenanceType::CORRECTIVE_MAINTENANCE,
            'status' => MaintenanceStatus::REPORTED,
            'executor_type' => ExecutorType::INTERNAL,
        ], $user);

        $this->assertNotEmpty($history->history_number);
        $this->assertMatchesRegularExpression('/^MTN\/\d{14}\/[A-Z0-9]{8}$/', $history->history_number);
        $this->assertNotEmpty($history->work_order_number);
        $this->assertMatchesRegularExpression('/^WO\/\d{14}\/[A-Z0-9]{8}$/', $history->work_order_number);
    }

    public function test_vendor_portal_can_upload_work_documents_and_store_them_on_history(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $user = User::factory()->create();
        $vendor = Vendor::factory()->create([
            'is_active' => true,
        ]);
        $equipment = Equipment::factory()->create([
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $history = EquipmentMaintenanceHistory::factory()->create([
            'equipment_id' => $equipment->id,
            'vendor_id' => $vendor->id,
            'executor_type' => ExecutorType::VENDOR->value,
            'status' => MaintenanceStatus::REPORTED->value,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $uploadedFile = UploadedFile::fake()->image('work-report.jpg', 640, 480);

        $this->withSession([
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name,
        ])->post(route('vendor.history.report', $history), [
            '_token' => csrf_token(),
            'status' => MaintenanceStatus::COMPLETED->value,
            'action_taken' => 'Service selesai dan equipment normal kembali.',
            'condition_after' => EquipmentCondition::GOOD->value,
            'attachments' => [$uploadedFile],
        ])->assertRedirect(route('vendor.history.show', $history));

        $history->refresh();

        $this->assertCount(1, $history->attachments()->get());
        $this->assertTrue(Storage::disk('public')->exists($history->attachments()->first()->file_path));
    }
}
