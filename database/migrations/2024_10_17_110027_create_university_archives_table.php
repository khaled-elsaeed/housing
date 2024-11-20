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
        Schema::create('university_archives', function (Blueprint $table) {
            $table->id();  // Primary key

            $table->string('name_en')->nullable();  // English name
            $table->string('name_ar')->nullable();  // Arabic name
            $table->string('university_id')->nullable()->unique();  // University ID (unique + index)
            $table->string('national_id')->nullable()->unique();  // National ID (unique + index)
            
            // Replaced foreign key with string columns
            $table->string('faculty')->nullable();  // Faculty ID (as string)
            $table->string('program')->nullable();  // Program ID (as string)

            $table->decimal('score', 5, 2)->nullable();  // Score
            $table->decimal('percent', 5, 2)->nullable();  // Percent

            $table->string('academic_email')->nullable()->unique();  // Academic Email (unique)
            $table->string('mobile')->nullable();  // Mobile number (index)
            $table->string('whatsapp')->nullable();  // WhatsApp number
            $table->date('birthdate')->nullable();  // Birthdate
            $table->string('gender')->nullable();  // Gender

            // Replaced foreign key with string columns
            $table->string('nationality')->nullable();  // Nationality ID (as string)
            $table->string('governorate')->nullable();  // Governorate ID (as string)
            $table->string('city')->nullable();  // City ID (as string)

            $table->string('street')->nullable();  // Street address
            $table->string('parent_name')->nullable();  // Parent name
            $table->string('parent_mobile')->nullable();  // Parent mobile number
            $table->string('parent_email')->nullable();  // Parent email
            $table->boolean('parent_is_abroad')->nullable();  // Is the parent abroad
            
            // Replaced foreign key with string columns
            $table->string('parent_abroad_country')->nullable();  // Parent abroad country ID (as string)

            $table->string('cert_type')->nullable();  // Certificate type

            // Replaced foreign key with string columns
            $table->string('cert_country')->nullable();  // Certificate country (as string)

            $table->year('cert_year')->nullable();  // Certificate year

            $table->string('sibling_name')->nullable();  // Sibling name
            
            // Replaced foreign key with string columns
            $table->string('sibling_faculty')->nullable();  // Sibling faculty ID (as string)

            $table->string('sibling_level')->nullable();  // Sibling level
            $table->boolean('has_sibling')->nullable();  // Does the student have a sibling

            $table->timestamps();  // Created at, Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_archives');
    }
};
