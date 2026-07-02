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
        Schema::table('smart_ads_payments', function (Blueprint $table) {
            $sm      = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('smart_ads_payments');
            if (isset($indexes['order_id_unique'])) {
                $table->dropUnique('order_id_unique');
            }
            $table->dropColumn('order_id');
        });

        Schema::table('smart_ads_payments', function (Blueprint $table) {
            $table->string('order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_ads_payments', function (Blueprint $table) {
            $table->dropColumn('order_id');
        });

        Schema::table('smart_ads_payments', function (Blueprint $table) {
            $table->string('order_id')->unique();
        });
    }
};
