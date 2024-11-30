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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');  // Reservation this payment is for

            // Payment details
            $table->decimal('amount', 10, 2)->nullable();  // Payment amount (nullable initially)
            $table->string('receipt_image')->nullable();  // Receipt image path (nullable initially)
            $table->enum('status', ['pending', 'accepted', 'rejected', 'refunded',])->default('pending');  // Payment status

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

