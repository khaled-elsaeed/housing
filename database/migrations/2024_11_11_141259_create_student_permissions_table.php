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
        Schema::create('student_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // E.g., 'Late Arrival', 'Overnight Leave'
            $table->string('category');  // E.g., 'Housing', 'Events', 'Academic'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_permissions');
    }
};
