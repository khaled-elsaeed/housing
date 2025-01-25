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
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('has_upcoming_reservation')->default(false);
            // Optional: Foreign key to link to the reservation
            $table->unsignedBigInteger('upcoming_reservation_id')->nullable();
            $table->foreign('upcoming_reservation_id')->references('id')->on('reservations')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['upcoming_reservation_id']);
            $table->dropColumn('upcoming_reservation_id');
            $table->dropColumn('has_upcoming_reservation');
        });
    }
};
