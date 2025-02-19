<?php

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
        // Create maintenance categories table
        Schema::create('maintenance_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create maintenance problems table
        Schema::create('maintenance_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('maintenance_categories')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create maintenance requests table
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('maintenance_categories');
            $table->text('description');
            $table->json('problems')->comment('Array of problem IDs and details');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('maintenance_problems');
        Schema::dropIfExists('maintenance_categories');
    }
};