<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('smart_ads_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // ✅ Added user_id foreign key
            $table->foreignId('smart_ad_id')->nullable()->constrained('smart_ads')->cascadeOnDelete();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->decimal('total_price', 10, 2);
            $table->decimal('daily_price', 8, 2);
            $table->integer('total_days');
            $table->json('price_summary')->nullable();
            $table->json('web_ads_placement')->nullable();
            $table->json('app_ads_placement')->nullable();
            $table->enum('ad_publish_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('smart_ads_details');
    }

};
