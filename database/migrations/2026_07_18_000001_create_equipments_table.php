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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('tag_no', 100)->unique();
            $table->string('technical_no', 150)->nullable()->index();
            $table->string('description');
            $table->string('functional_location')->nullable()->index();
            $table->string('manufacturer')->nullable();
            $table->string('model_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('category')->nullable()->index();
            $table->date('installation_date')->nullable();
            $table->string('status')->default('operational')->index();
            $table->string('latest_condition')->nullable();
            $table->dateTime('last_maintenance_at')->nullable();
            $table->dateTime('next_maintenance_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
