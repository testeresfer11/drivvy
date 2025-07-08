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
       Schema::create('refund_payment', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('payment_id'); // Payment ID to which the refund is associated
            $table->decimal('refunded_amount', 10, 2); // Amount refunded
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_payment');
    }
};
