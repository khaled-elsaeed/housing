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
            $table->id(); // Primary key

            // Foreign key constraints
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete()->cascadeOnUpdate();

            // Academic year and term
            $table->year('year'); 
            $table->enum('term', ['first_term', 'second_term', 'summer']); 

            // Dates for the reservation
            $table->date('start_date')->nullable; 
            $table->date('end_date')->nullable; 

            // Status of the reservation
            $table->enum('status', ['pending', 'active', 'cancelled', 'completed'])->default('pending'); 

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


