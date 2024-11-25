<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('issue_type'); // Issue type (Plumbing, Electrical, etc.)
            $table->text('description'); // Details of the issue

            // Using enum for status
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending'); // Enum for status
            
            $table->timestamp('requested_at')->useCurrent(); // Timestamp for when request was made
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
