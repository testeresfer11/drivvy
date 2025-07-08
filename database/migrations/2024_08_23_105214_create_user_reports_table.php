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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('ride_id');
            $table->unsignedBigInteger('report_id');
            $table->string('description');
            $table->timestamps();
            $table->foreign('driver_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('passenger_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('ride_id')->references('ride_id')->on('rides')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
