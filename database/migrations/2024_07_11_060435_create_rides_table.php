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
        Schema::create('rides', function (Blueprint $table) {
            $table->id('ride_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('car_id');
            $table->string('departure_city', 100)->nullable();
            $table->string('arrival_city', 100)->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->decimal('price_per_seat', 8, 2);
            $table->integer('available_seats')->nullable();
            $table->string('luggage_size', 50)->nullable();
            $table->boolean('smoking_allowed')->default(false);
            $table->boolean('pets_allowed')->default(false);
            $table->string('music_preference', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('driver_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('car_id')->references('car_id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
