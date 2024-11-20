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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            
            // User foreign key
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->string('name'); // Parent's name
            $table->string('relation')->nullable(); // Relation to the child
            $table->string('email')->nullable(); // Email
            $table->string('mobile')->nullable(); // Mobile number
            
            $table->boolean('living_abroad')->default(false); // Living abroad status
            
            // Abroad country foreign key
            $table->foreignId('abroad_country_id')
                ->nullable()
                ->constrained('countries')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->string('living_with')->nullable(); // Living with

            // Governorate foreign key
            $table->foreignId('governorate_id')
                ->nullable()
                ->constrained('governorates')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // City foreign key
            $table->foreignId('city_id')
                ->nullable()
                ->constrained('cities')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->timestamps(); // Created at, Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
