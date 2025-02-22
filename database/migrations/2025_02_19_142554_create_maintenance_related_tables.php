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
            $table->string('name_en'); // English name
            $table->string('name_ar'); // Arabic name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create maintenance problems table
        Schema::create('maintenance_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('maintenance_categories')->onDelete('cascade');
            $table->string('name_en'); // English name
            $table->string('name_ar'); // Arabic name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create maintenance requests table
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('maintenance_categories');
            
            // Request details
            $table->text('description');            
            // Status tracking
            $table->enum('status', ['pending', 'accepted', 'rejected', 'assigned', 'in_progress', 'completed'])->default('pending');
            
            // Assigned technician
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            // Timestamps for tracking
            $table->timestamp('assigned_at')->nullable(); 
            $table->timestamp('staff_accepted_at')->nullable(); 

            $table->timestamp('rejected_at')->nullable(); 
            $table->text('reject_reason')->nullable(); 
            $table->timestamp('completed_at')->nullable(); 
            
            // Default timestamps
            $table->timestamps();
        });

        
        Schema::create('maintenance_problem_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('maintenance_requests')->onDelete('cascade');
            $table->foreignId('problem_id')->constrained('maintenance_problems')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('maintenance_problem_request');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('maintenance_problems');
        Schema::dropIfExists('maintenance_categories');
    }
};
