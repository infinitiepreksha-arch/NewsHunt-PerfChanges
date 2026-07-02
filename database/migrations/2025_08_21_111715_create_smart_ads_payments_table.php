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
        Schema::create('smart_ads_payments', function (Blueprint $table) {
            $table->id();

            // Link to the smart ad
            $table->unsignedBigInteger('smart_ad_id');
            $table->foreign('smart_ad_id')
                ->references('id')
                ->on('smart_ads')
                ->onDelete('cascade');

            // Link to the user who paid
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_gateway')->nullable(); // stripe, razorpay, applepay
            $table->string('transaction_id')->nullable();
            $table->json('transaction_details')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_ads_payments');
    }
};
