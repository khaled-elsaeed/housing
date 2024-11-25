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
    Schema::create('maintenance_request_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('maintenance_request_id')  
        ->unique()
        ->constrained('maintenance_requests')
        ->onDelete('cascade')
        ->onUpdate('cascade');        $table->string('image_path'); // Path to the image
        $table->timestamps();

    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_request_images');
    }
};
