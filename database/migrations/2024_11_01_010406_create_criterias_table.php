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
        Schema::create('criterias', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('field_id')->constrained()->onDelete('cascade'); // Foreign key to fields table
            $table->string('criteria'); // Description of the criterion
            $table->integer('weight'); // Weight assigned to the criterion
            $table->string('type'); // Type of the criterion
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterias');
    }
};
