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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('building_id')->constrained('buildings')->cascadeOnDelete()->cascadeOnUpdate(); // FK to buildings
            $table->integer('number'); // Unique apartment number
            $table->integer('max_rooms'); // Maximum number of rooms
            $table->enum('occupancy_status', ['empty', 'partially_occupied', 'full_occupied'])->default('empty'); // Occupancy status            $table->integer('current_occupancy')->default(0); // Current occupancy count
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active'); // Status
            $table->string('description', 250)->nullable(); // Description of the apartment
            $table->timestamps(); // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
