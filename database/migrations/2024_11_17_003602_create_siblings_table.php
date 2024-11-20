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
        Schema::create('siblings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')  
            ->unique()
            ->constrained('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->string('name');
            $table->string('national_id')->nullable();
             
            // Faculty foreign key
            $table->foreignId('faculty_id')
                ->nullable()
                ->constrained('faculties')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('gender')->nullable();
            $table->boolean('share_room')->nullable(); // Indicates whether they share a room with the student
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siblings');
    }
};
