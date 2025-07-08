<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->boolean('max_two_back')->default(false);
            $table->boolean('women_only')->default(false);
            $table->string('stopovers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropColumn('max_two_back');
        $table->dropColumn('women_only');
        $table->dropColumn('stopovers');

    }
};
