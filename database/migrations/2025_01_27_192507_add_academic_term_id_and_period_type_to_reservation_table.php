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
        Schema::table('reservations', function (Blueprint $table) {
            
            // Add period_type column with enum values 'short' and 'long'
            $table->enum('period_type', ['short', 'long'])->default('long');

            // Add academic_term_id column as a foreign key
            $table->unsignedBigInteger('academic_term_id')->nullable();

            // Add foreign key constraint
            $table->foreign('academic_term_id')
                  ->references('id')
                  ->on('academic_terms')
                  ->onDelete('restrict'); // Prevents deletion if related records exist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['academic_term_id']);

            // Drop the academic_term_id column
            $table->dropColumn('academic_term_id');

            // Drop the period_type column
            $table->dropColumn('period_type');
        });
    }
};