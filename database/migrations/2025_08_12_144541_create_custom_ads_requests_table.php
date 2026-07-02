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
        Schema::create('custom_ads_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('ad_type', ['image']);
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->decimal('total_price', 10, 2);
            $table->decimal('daily_price', 8, 2);
            $table->integer('total_days');
            $table->json('price_summary')->nullable();
            $table->json('web_ads_placement')->nullable(); 
            $table->json('app_ads_placement')->nullable();
            $table->unsignedInteger('ad_clicks')->default(0);
            $table->enum('ad_publish_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_ads_requests');
    }
};
