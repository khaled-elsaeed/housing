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
        Schema::create('university_archieve', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->string('national_id', 45)->unique();
            $table->string('mobile', 20)->unique();
            $table->date('birthdate');
            $table->enum('gender', ['male', 'female']);
            $table->foreignId('city')->constrained('cities')->onDelete('restrict')->onUPdate('cascade');
            $table->string('street', 250);
            $table->string('parent_name', 150);
            $table->string('parent_email', 100);
            $table->string('parent_mobile', 45);
            $table->boolean('parent_is_abroad');
            $table->foreignId('parent_abroad_country_id')->nullable()->constrained('countries')->onDelete('restrict')->onUpdate('cascade');
            $table->string('sibling_name', 100)->nullable();
            $table->string('sibling_national_id', 45)->nullable()->unique();
            $table->foreignId('sibling_faculty_id')->nullable()->constrained('faculties')->onDelete('restrict')->onUpdate('cascade');;
            $table->string('sibling_mobile', 45)->nullable();
            $table->enum('sibling_gender', ['male', 'female'])->nullable();
            $table->boolean('has_sibling')->default(0);
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('score', 5, 2);
            $table->decimal('percent', 5, 2)->nullable();
            $table->string('academic_email', 100)->nullable()->unique();
            $table->string('cert_type', 100)->nullable();
            $table->string('cert_country', 100)->nullable();
            $table->year('cert_year')->nullable();
            $table->boolean('is_new_comer')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_archieve');
    }
};
