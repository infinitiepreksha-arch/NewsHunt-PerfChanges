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
        Schema::table('smart_ads_tracking', function (Blueprint $table) {
            $table->foreignId('smart_ad_id')
                ->nullable()
                ->after('id')
                ->constrained('smart_ads')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_ads_tracking', function (Blueprint $table) {
            $table->dropForeign(['smart_ad_id']);
            $table->dropColumn('smart_ad_id');
        });
    }
};
