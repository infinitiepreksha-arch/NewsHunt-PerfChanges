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
        Schema::create('custom_ads_trackings', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('ad_request_id')->constrained('custom_ads_requests')->cascadeOnDelete();
            $table->json('ad_clicks')->nullable(); // stores timestamps, IP, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_ads_trackings');
    }
};
