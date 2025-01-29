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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_picture');

            $table->foreignId('media_id') // Creates a `media_id` column
            ->nullable()              // Allows NULL values
            ->constrained('media')    // References the `id` column in the `media` table
            ->onDelete('set null');   // Sets `media_id` to NULL when the referenced `media` record is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
