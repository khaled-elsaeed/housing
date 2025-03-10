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
       
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); 
            $table->string('category'); 
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable(); 
            $table->enum('status',['paid','unpaid'])->default('unpaid');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
