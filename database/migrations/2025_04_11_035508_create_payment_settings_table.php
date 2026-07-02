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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            
            // Common fields
            $table->string('gateway')->comment('stripe or razorpay');
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->boolean('status')->default(false); // enable/disable
        
            // Stripe fields
            $table->string('stripe_secret')->nullable();
            $table->string('stripe_publishable')->nullable();
            $table->string('stripe_webhook_secret')->nullable();
            $table->string('stripe_webhook_url')->nullable();
        
            // Razorpay fields
            $table->string('razorpay_secret')->nullable();
            $table->string('razorpay_key')->nullable();
            $table->string('razorpay_webhook_secret')->nullable();
            $table->string('razorpay_webhook_url')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
