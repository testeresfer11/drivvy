<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('chattiness','50')->nullable();
            $table->string('music','50')->nullable();
            $table->string('smoking','50')->nullable();
            $table->string('pets','50')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('chattiness'); 
            $table->dropColumn('music'); 
            $table->dropColumn('smoking'); 
            $table->dropColumn('pets'); 
        });
    }
};
