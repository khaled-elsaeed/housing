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
        Schema::table('invoices', function (Blueprint $table) {
            // Add rank column
            $table->unsignedInteger('rank')->nullable()->after('status'); // Adjust the position as needed

            // Add image_path column
            $table->string('image_path')->nullable()->after('rank'); // Adjust the position as needed

            // Add foreign key for media_id
            $table->foreignId('media_id')->nullable()->constrained('media')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop foreign key and media_id column
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');

            // Drop rank and image_path columns
            $table->dropColumn('rank');
            $table->dropColumn('image_path');
        });
    }
};