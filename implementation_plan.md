# Equipment Maintenance History Module Implementation Plan

This plan details the implementation of the **Equipment Maintenance History** module. The module is designed to manage equipment masters, track vendor or internal maintenance actions, record spare part consumption, manage associated files/attachments, and import equipment lists from Excel files.

Per your instructions, **no Filament resources, pages, or forms will be created**; the implementation focuses purely on the backend structure (migrations, models, enums, services, action classes, form requests, Excel imports, policies, factories, seeders, and tests) so that you can easily integrate it with Filament yourself.

---

## Design Decisions & Architecture

To ensure a robust, maintainable, and production-ready implementation, we propose the following key design decisions:

### 1. Database Constraints & Deletions
* **`equipment_id` Foreign Key**: We use `restrictOnDelete()` for the relationship between `equipment_maintenance_histories` and `equipments`. This prevents accidental loss of critical maintenance audit logs if someone attempts to delete an equipment. Since `equipments` supports **soft deletes**, the equipment can be soft-deleted without issues, preserving the integrity of the histories.
* **`spare_part_id` Foreign Key**: We use `restrictOnDelete()` for the pivot table `equipment_maintenance_spare_parts`. If a spare part has historical usage, it cannot be hard deleted. This preserves cost records and financial compliance.

### 2. Document Sequence & Concurrency
* **`DocumentNumberService`**: We will implement a thread-safe sequence generator using the `document_sequences` table. It will use a DB transaction combined with a `lockForUpdate()` query (pessimistic locking) to fetch and increment the last sequence number for the current year/month. This guarantees that concurrent requests will queue instead of generating duplicate history numbers (e.g. `MTN/2026/07/000001`).

### 3. File Attachment & Orphan Prevention
* **Upload Path & Storage**: Physical attachments will be stored using the `Storage` facade on the `public` disk (or configured disk) under `maintenance-attachments/{year}/{month}/`.
* **Database & Storage Consistency**:
  * **Upload Clean-up on Failure**: If a database transaction rolls back during record creation/update, any physical files uploaded during that transaction will be caught in a `catch` block and deleted immediately.
  * **Delete on Commit**: When deleting a history or attachment, the database record will be deleted first, and the physical file deletion will be scheduled via Laravel's `DB::afterCommit(callable)` callback. This ensures physical files are only deleted if the database change is fully committed, preventing broken links.

### 4. Cost Calculations
* **`total_cost`**: Automatically calculated as the sum of `labor_cost`, `material_cost`, and `other_cost` in the model's `saving` event to ensure data consistency.
* **`material_cost`**: Computed in the Action classes (`CreateMaintenanceHistoryAction` and `UpdateMaintenanceHistoryAction`) by summing up `quantity * unit_price` of the associated spare parts.

---

## User Review Required

> [!IMPORTANT]
> 1. **Role Integration**: In the policies, we assume the `User` model uses Spatie's Laravel Permission package (via `HasRoles` trait) since `app/Models/User.php` already imports it. We will call `$user->hasRole(...)` directly to authorize actions.
> 2. **External Packages**: We will install `maatwebsite/excel` for importing. It will be added via composer.
> 3. **Pest Testing**: We will write Pest feature and unit tests inside the `tests/Feature` and `tests/Unit` directories, as Pest is the default test framework configured in the codebase.

---

## Proposed Changes

We will group our proposed files as follows:

### 1. Enums
We will create backing string enums inside `app/Enums/`:
* [NEW] [EquipmentStatus.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/EquipmentStatus.php): Represents the current state of an equipment.
* [NEW] [MaintenanceType.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/MaintenanceType.php): Categorizes the type of maintenance action performed.
* [NEW] [MaintenanceStatus.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/MaintenanceStatus.php): Tracks lifecycle stages of a maintenance history.
* [NEW] [ExecutorType.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/ExecutorType.php): Internal, Vendor, or Combination.
* [NEW] [EquipmentCondition.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/EquipmentCondition.php): Condition status of the equipment.
* [NEW] [AttachmentCategory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Enums/AttachmentCategory.php): Classifies uploaded files.

### 2. Migrations
We will create migrations inside `database/migrations/` in logical dependency order:
* [NEW] `create_equipments_table`: Equipment master.
* [NEW] `create_vendors_table`: Vendor directories.
* [NEW] `create_spare_parts_table`: Spare parts inventory.
* [NEW] `create_document_sequences_table`: Sequence numbers for document number generation.
* [NEW] `create_equipment_maintenance_histories_table`: Maintenance histories.
* [NEW] `create_equipment_maintenance_spare_parts_table`: Pivot table mapping history to spare parts with quantities and prices.
* [NEW] `create_equipment_maintenance_attachments_table`: Reference table for uploaded files.

### 3. Models
We will create models inside `app/Models/` with type hints, scopes, and model events:
* [NEW] [Equipment.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/Equipment.php)
* [NEW] [Vendor.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/Vendor.php)
* [NEW] [SparePart.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/SparePart.php)
* [NEW] [DocumentSequence.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/DocumentSequence.php)
* [NEW] [EquipmentMaintenanceHistory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/EquipmentMaintenanceHistory.php)
* [NEW] [EquipmentMaintenanceSparePart.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/EquipmentMaintenanceSparePart.php)
* [NEW] [EquipmentMaintenanceAttachment.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Models/EquipmentMaintenanceAttachment.php)

### 4. Data Transfer Objects (DTO)
We will create readonly DTOs inside `app/Data/`:
* [NEW] [EquipmentData.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Data/EquipmentData.php)
* [NEW] [MaintenanceHistoryData.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Data/MaintenanceHistoryData.php)
* [NEW] [MaintenanceSparePartData.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Data/MaintenanceSparePartData.php)

### 5. Services
We will create service classes inside `app/Services/`:
* [NEW] [DocumentNumberService.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Services/DocumentNumberService.php): Safe sequential number generator.
* [NEW] [AttachmentService.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Services/AttachmentService.php): Disk and DB attachment manager.
* [NEW] [EquipmentDuplicateDetectionService.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Services/EquipmentDuplicateDetectionService.php): Smart search for potential duplicate equipment entries.

### 6. Action Classes
We will create action classes inside `app/Actions/`:
* [NEW] [CreateEquipmentAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Equipment/CreateEquipmentAction.php)
* [NEW] [UpdateEquipmentAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Equipment/UpdateEquipmentAction.php)
* [NEW] [CreateMaintenanceHistoryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/CreateMaintenanceHistoryAction.php)
* [NEW] [UpdateMaintenanceHistoryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/UpdateMaintenanceHistoryAction.php)
* [NEW] [CompleteMaintenanceHistoryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/CompleteMaintenanceHistoryAction.php)
* [NEW] [CancelMaintenanceHistoryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/CancelMaintenanceHistoryAction.php)
* [NEW] [DeleteMaintenanceHistoryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/DeleteMaintenanceHistoryAction.php)
* [NEW] [RecalculateEquipmentSummaryAction.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Actions/Maintenance/RecalculateEquipmentSummaryAction.php)

### 7. Form Requests
We will create form requests inside `app/Http/Requests/` with Indonesian validation messages for important rules:
* [NEW] [StoreEquipmentRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/StoreEquipmentRequest.php)
* [NEW] [UpdateEquipmentRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/UpdateEquipmentRequest.php)
* [NEW] [StoreVendorRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/StoreVendorRequest.php)
* [NEW] [UpdateVendorRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/UpdateVendorRequest.php)
* [NEW] [StoreSparePartRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/StoreSparePartRequest.php)
* [NEW] [UpdateSparePartRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/UpdateSparePartRequest.php)
* [NEW] [StoreMaintenanceHistoryRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/StoreMaintenanceHistoryRequest.php)
* [NEW] [UpdateMaintenanceHistoryRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/UpdateMaintenanceHistoryRequest.php)
* [NEW] [CompleteMaintenanceHistoryRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/CompleteMaintenanceHistoryRequest.php)
* [NEW] [ImportEquipmentRequest.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Http/Requests/ImportEquipmentRequest.php)

### 8. Excel Import
We will create:
* [NEW] [HeaderNormalizer.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Support/HeaderNormalizer.php): Utility to clean and normalize Excel headers.
* [NEW] [EquipmentImport.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Imports/EquipmentImport.php): Handles mapping, validating, and upserting rows of equipment data.

### 9. Policies
We will create policies inside `app/Policies/` checking User roles:
* [NEW] [EquipmentPolicy.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Policies/EquipmentPolicy.php)
* [NEW] [VendorPolicy.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Policies/VendorPolicy.php)
* [NEW] [SparePartPolicy.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Policies/SparePartPolicy.php)
* [NEW] [EquipmentMaintenanceHistoryPolicy.php](file:///home/alwizen/Workspace/PTM/kaido-kit/app/Policies/EquipmentMaintenanceHistoryPolicy.php)

### 10. Seeders & Factories
We will create seeders and factories:
* [NEW] [EquipmentFactory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/factories/EquipmentFactory.php)
* [NEW] [VendorFactory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/factories/VendorFactory.php)
* [NEW] [SparePartFactory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/factories/SparePartFactory.php)
* [NEW] [EquipmentMaintenanceHistoryFactory.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/factories/EquipmentMaintenanceHistoryFactory.php)
* [NEW] [EquipmentSeeder.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/seeders/EquipmentSeeder.php)
* [NEW] [VendorSeeder.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/seeders/VendorSeeder.php)
* [NEW] [SparePartSeeder.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/seeders/SparePartSeeder.php)
* [NEW] [EquipmentMaintenanceHistorySeeder.php](file:///home/alwizen/Workspace/PTM/kaido-kit/database/seeders/EquipmentMaintenanceHistorySeeder.php)

---

## Verification Plan

### Automated Tests
We will write a comprehensive suite of Pest tests under:
* `tests/Feature/EquipmentTest.php`
* `tests/Feature/MaintenanceHistoryTest.php`
* `tests/Feature/EquipmentImportTest.php`
* `tests/Feature/PolicyTest.php`
* `tests/Unit/DocumentNumberConcurrencyTest.php`

To run the automated tests, we will execute:
```bash
./vendor/bin/pest
```

### Manual Verification
1. Run seeders via `php artisan db:seed` to ensure sample data inserts correctly.
2. Review database schema tables and relationships in MySQL to confirm integrity constraints.
3. Validate action executions manually using a Tinker script to ensure database transactions, total calculation, and file cleanup function as designed.
