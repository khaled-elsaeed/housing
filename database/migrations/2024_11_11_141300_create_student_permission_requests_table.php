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
        Schema::create('student_permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('student_permission_id')->constrained('student_permissions')->onDelete('cascade');  // Link to the dorm permission
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->text('reason')->nullable();  // Reason for the request
            $table->datetime('requested_at')->nullable();  // Date/time when the request is made
            $table->text('admin_comments')->nullable();  // Optional comments by the admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_permission_requests');
    }
};
