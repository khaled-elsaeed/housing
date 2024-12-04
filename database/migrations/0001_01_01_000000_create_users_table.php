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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('gender', ['male', 'female']);
            $table->string('first_name_ar', 100)->nullable();
            $table->string('last_name_ar', 100)->nullable();
            $table->string('first_name_en', 100)->nullable();
            $table->string('last_name_en', 100)->nullable();
            $table->string('activation_token')->nullable();
            $table->timestamp('activation_expires_at')->nullable();
            $table->string('profile_picture', 255)->nullable(); 
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', ['active', 'Suspended'])->default('active'); 
            $table->boolean('profile_completed')->default(0); // Profile completion status
            $table->timestamp('profile_completed_at')->nullable(); // Profile completion timestamp
            $table->boolean('can_complete_late')->default(0); // Late completion permission
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
        

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id(); 
            $table->string('email')->unique(); // Unique email column
            $table->string('token'); // Token column
            $table->timestamp('token_expires_at'); // Expiration timestamp
            $table->timestamps(); // Created at and updated at timestamps
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
