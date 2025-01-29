<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('admin_id')->nullable(); // ID of the admin who performed the action
            $table->unsignedBigInteger('user_id')->nullable(); // ID of the user the action is related to
            $table->string('activity_type'); // Type of activity (e.g., login, update, delete)
            $table->text('description'); // Description of the activity
            $table->timestamps(); // created_at and updated_at timestamps

            // Foreign key constraints (optional)
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
