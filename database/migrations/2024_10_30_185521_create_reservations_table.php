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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id'); // Primary key

            // Foreign key constraints
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete()->cascadeOnUpdate();

            // Reservation details
            $table->dateTime('start_date'); // Start date of the reservation
            $table->dateTime('end_date'); // End date of the reservation
            $table->enum('status', ['pending', 'active', 'cancelled', 'completed'])->default('pending'); // Status of the reservation
            
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
