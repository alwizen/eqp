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
        Schema::create('equipment_maintenance_attachments', function (Blueprint $table) {
            $table->id();
            
            // Shortened foreign key constraint name to prevent MySQL 64-char limit error
            $table->foreignId('equipment_maintenance_history_id')
                ->constrained('equipment_maintenance_histories', 'id', 'ema_history_fk')
                ->cascadeOnDelete();
            
            $table->string('category')->default('other');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_attachments');
    }
};
