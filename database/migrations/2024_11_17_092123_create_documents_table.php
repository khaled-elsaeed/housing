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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 

        
            $table->foreignId('reservation_id')
                ->nullable() 
                ->constrained('reservations')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 

            $table->enum('type', ['invoice', 'other']);
            
            $table->string('document_path')->unique();

            $table->enum('status', ['pending', 'preliminary_accepted', 'final_accepted'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the documents table if rolled back
        Schema::dropIfExists('documents');
    }
};
