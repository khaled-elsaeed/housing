<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_requests', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->unsignedBigInteger('academic_term_id')->nullable(); // Foreign key to academic_terms table
            $table->string('gender'); // User's gender preference (e.g., male, female)
            $table->string('period_type'); // Reservation period type (e.g., long, short)
            $table->string('period_duration')->nullable();
            $table->date('start_date')->nullable(); // Start date of the reservation
            $table->date('end_date')->nullable(); // End date of the reservation
            $table->string('status')->default('pending'); // Request status (e.g., pending, assigned)
            $table->timestamps(); // created_at and updated_at timestamps

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_requests');
    }
}
