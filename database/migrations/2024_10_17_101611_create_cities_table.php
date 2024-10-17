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
        Schema::create('cities', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('governorate_id')
            ->constrained('governorates')
            ->onDelete('cascade')
            ->onUpdate('cascade'); 
            $table->string('name_ar', 200); 
            $table->string('name_en', 200); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
