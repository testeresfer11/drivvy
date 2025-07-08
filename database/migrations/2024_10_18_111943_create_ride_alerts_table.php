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
         Schema::create('ride_alerts', function (Blueprint $table) {
        $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('departure_time');
            $table->string('departure_city');
            $table->string('arrival_city');
            $table->double('user_departure_lat');
            $table->double('user_departure_long');
            $table->double('user_arrival_lat');
            $table->double('user_arrival_long');
            $table->integer('seat_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_alerts');
    }
};
