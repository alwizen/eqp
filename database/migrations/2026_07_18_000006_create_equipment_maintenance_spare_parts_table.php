<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_maintenance_spare_parts', function (Blueprint $table) {
            $table->id();
            
            // Explicitly set short foreign key names to prevent MySQL 64-char limit error
            $table->foreignId('equipment_maintenance_history_id')
                ->constrained('equipment_maintenance_histories', 'id', 'emsp_history_fk')
                ->cascadeOnDelete();
            
            $table->foreignId('spare_part_id')
                ->constrained('spare_parts', 'id', 'emsp_spare_part_fk')
                ->restrictOnDelete();
            
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['equipment_maintenance_history_id', 'spare_part_id'], 'history_spare_part_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_spare_parts');
    }
};
