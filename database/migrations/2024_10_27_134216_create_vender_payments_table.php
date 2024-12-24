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
        Schema::create('vender_payments', function (Blueprint $table) {
            $table->id();
            $table->string('paid_amount');
            $table->string('remaining_amount');
            $table->string('payment_type');
            $table->string('remarks');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('exchange_id')->nullable()->constrained('exchanges')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vender_payments');
    }
};
