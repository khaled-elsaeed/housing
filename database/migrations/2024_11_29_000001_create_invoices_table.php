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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->decimal('amount', 10, 2); 
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid'); 
            $table->enum('category', ['fee', 'insurance', 'other'])->default('fee'); 

            $table->timestamps(); // Created at, updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
