<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name_en', 250);
            $table->string('name_ar', 250);
            $table->string('national_id', 45)->unique();
            $table->string('mobile', 20)->unique();
            $table->date('birthdate');
            $table->enum('gender', ['male', 'female']);
            $table->foreignId('city')->constrained('cities')->onDelete('restrict')->onUPdate('cascade');
            $table->string('street', 250);
            $table->boolean('profile_completed')->default(0); 
            $table->timestamp('profile_completed_at')->nullable();
            $table->boolean('can_complete_late')->default(0);
            $table->foreignId('university_archieve_id')->unique()->constrained('university_archieve')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
