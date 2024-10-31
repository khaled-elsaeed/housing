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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->integer('number')->unique(); // Unique building number
            $table->enum('gender', ['male', 'female']); // Gender of the building
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active'); // Status
            $table->string('description', 250)->nullable(); // Description of the building
            $table->integer('max_apartments')->nullable(); // Max number of apartments in the building
            $table->timestamps(); // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};

