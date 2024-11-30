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
            $table->id(); // Primary key
            $table->foreignId('user_id')  // Foreign key referencing users table
                ->unique()
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->string('name_en', 250); // English name
            $table->string('name_ar', 250); // Arabic name
            $table->string('national_id')->nullable()->unique();
            $table->string('academic_id')->nullable()->unique();
            $table->string('mobile', 20); // Mobile number
            $table->date('birthdate'); // Birthdate
            $table->enum('gender', ['male', 'female']); // Gender
            
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
            
            $table->string('street', 250); // Street

            $table->foreignId('faculty_id') // Foreign key referencing faculties table
                ->nullable()
                ->constrained('faculties')
                ->onDelete('restrict')
                ->onUpdate('cascade');
                $table->foreignId('program_id') // Foreign key referencing faculties table
                ->nullable()
                ->constrained('programs')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->boolean('profile_completed')->default(0); // Profile completion status
            $table->timestamp('profile_completed_at')->nullable(); // Profile completion timestamp
            $table->boolean('can_complete_late')->default(0); // Late completion permission
            
            $table->foreignId('university_archive_id') // Foreign key referencing university_archives table
                ->unique()
                ->constrained('university_archives')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->enum('application_status', ['pending', 'preliminary_accepted', 'final_accepted'])->default('pending'); // Application status
            
            $table->timestamps(); // Created at, Updated at timestamps
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
