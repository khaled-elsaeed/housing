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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete()->cascadeOnUpdate(); // FK to apartments
            $table->integer('number')->unique(); // Unique room number
            $table->tinyInteger('full_occupied')->default(0); // Full occupancy flag
            $table->integer('max_occupancy'); // Maximum number of occupants
            $table->integer('current_occupancy')->default(0); // Current occupancy count
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active'); // Status
            $table->enum('type', ['accommodation', 'office', 'utility'])->default('accommodation'); // Type of room
            $table->string('description', 255)->nullable(); // Description of the room
            $table->timestamps(); // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};